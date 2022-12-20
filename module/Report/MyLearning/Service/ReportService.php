<?php

namespace Report\MyLearning\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ReportService extends AbstractService
{

    /**
     * Create the report
     *
     * @param array $filter Array of filters to use to generate the report
     * @return array $report Array containing the data of the report
     */
    public function report (array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $siteId         = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $activityIds    = array_key_exists('activity_id', $filter) && strlen($filter['activity_id']) ? (array) $filter['activity_id'] : null;
        $planIds        = array_key_exists('plan_id', $filter) && strlen($filter['plan_id']) ? (array) $filter['plan_id'] : null;
        $groupIds       = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : null;
        $learnerIds     = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $showFrom       = array_key_exists('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
        $showTo         = array_key_exists('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
        $allDates       = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $filterByDates  = ($showFrom || $showTo) ? true : false;
        $trackingStatus = array_key_exists('tracking_status', $filter) ? $filter['tracking_status'] : null;
        $learnerStatus  = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;
        $orderBy        = array_key_exists('order_by', $filter) ? (array) $filter['order_by'] : ['learner.firstName ASC', 'learner.lastName ASC', 'activity.title ASC', 'tracking_status_level DESC'];
        $params = [];

        // create query
        $dql = [];
        $dql[] = "SELECT
                	partial distribution.{distributionId},
                	activity.activityId AS activity_id,
                	activity.activityType AS activity_type,
                	activity.title AS activity_title,
                	activity.cpd AS activity_cpd,
                	learner.userId AS learner_id,
                	learner.firstName AS learner_first_name,
                	learner.lastName AS learner_last_name,
                	CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                	learner.status AS learner_status,
                	site.siteId AS site_id,
                	site.name AS site_name,
                	trackerActivity.score AS tracking_score,
                	plans.planId AS plan_id,
                	plans.title AS plan_title,
                	(CASE
                		WHEN trackerActivity.completionStatus IN ('not-attempted') OR trackerActivity.completionStatus IS NULL THEN 'not-attempted'
                		WHEN trackerActivity.completionStatus IN ('completed', 'complete') THEN 'completed'
                		ELSE trackerActivity.completionStatus
                	END) AS tracking_status,
                	(CASE
	                	WHEN trackerActivity.completionStatus IN ('completed', 'complete') THEN 2
	                	WHEN trackerActivity.completionStatus IN ('not-attempted') OR trackerActivity.completionStatus IS NULL THEN -1
	                	ELSE 1
	                END) AS HIDDEN tracking_status_level,
			        trackerActivity.startedOn AS tracking_started,
			        trackerActivity.lastAccessed AS tracking_last_accessed,
			        trackerActivity.completedOn AS tracking_completed,
                	distribution.distributionDate AS distribution_date,
                	distribution.expiryDate AS expiry_date

                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN activity.hasPlans hasPlans
                LEFT JOIN hasPlans.plans plans
                LEFT JOIN learner.site site
                LEFT JOIN distribution.trackerActivity trackerActivity
                WHERE site.siteId = :siteId
                AND activity.status IN (:activityStatus)
                AND distribution.status IN (:distributionStatus)
                ";

		//site.site_id
        $params['siteId'] = $siteId;
		//learning_activity.status
		$params['activityStatus'] = ['active','inactive','new']; //@todo : Should inactive come up ?
		$params['distributionStatus'] = ['active','new','expired','approved'];

        // learner IDs
		$dql[] = "AND learner.userId IN (:learnerId)";
        $params['learnerId'] = $learnerIds;


        // learner status
        if ($learnerStatus) {
            $dql[] = "AND learner.status IN (:learnerStatus)";
            $params['learnerStatus'] = $learnerStatus;
        }

        // if activity IDs is provided, filter results by activity IDs
        if ($activityIds && count($activityIds) >= 1) {
            $dql[] = "AND activity.activityId IN (:activityId)";
            $params['activityId'] = $activityIds;
        }

        // if plan IDs is provided, filter results by plan (playlist) IDs
        if ($planIds) {
            $dql[] = "AND plans.planId IN (:planId)";
            $params['planId'] = $planIds;
        }

        // tracking and distribution status
        if ($trackingStatus) {
            $dql[] = "AND ("
                    . "trackerActivity.completionStatus IN (:trackingStatus)"
					. (in_array('not-attempted', $trackingStatus) ? ' OR trackerActivity.completionStatus IS NULL' : '')
                    .")";
            $params['trackingStatus'] = $trackingStatus;
        }

        // date range
        if ($filterByDates) {
            $dateFilter = [];

            // if tracking status is set, filter dates based on the selected tracking status
            if ($trackingStatus) {
	            // show based on completion date
	            if (array_intersect(['completed', 'passed'], $trackingStatus)) {
		            $dateFilter[] = ($showFrom ? "trackerActivity.completedOn >= :showFrom" : '')
		                    . ($showFrom && $showTo ? "AND" : '')
		                    . ($showTo ? "trackerActivity.completedOn <= :showTo" : '');
	            }

	            // show based on distribution date
	            if (in_array('not-attempted', $trackingStatus)) {
					$dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '')
		                    . ($showFrom && $showTo ? "AND" : '')
		                    . ($showTo ? "distribution.distributionDate <= :showTo" : '');
	            }

	            // show based on tracker started date
	            if (array_intersect(['failed','incomplete'], $trackingStatus)) {
	                $dateFilter[] = ($showFrom ? "trackerActivity.lastAccessed >= :showFrom" : '')
			                . ($showFrom && $showTo ? "AND" : '')
			                . ($showTo ? "trackerActivity.lastAccessed <= :showTo" : '');
	            }
            }

            // use the distribution date by default
            else {
                $dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '')
		                . ($showFrom && $showTo ? "AND" : '')
		                . ($showTo ? "distribution.distributionDate <= :showTo" : '');
            }

            if ($dateFilter) {
                $dql[] = sprintf("AND (%s)", implode("OR", $dateFilter));
                $showFrom ? $params['showFrom'] = $showFrom : null;
                $showTo ? $params['showTo'] = $showTo : null;
            }
        }

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // order by
        if ($orderBy) {
            $orderBy = implode(", ", $orderBy);
            $dql[] = sprintf("ORDER BY %s", $orderBy);
        }

        // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
        	->setParameters($params)
        	->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // trigger event listeners
        $eventManager = $this->getEventManager();
        $eventResults = $eventManager->trigger(new Event(Event::EVENT_REPORT_POST, $this, [ 'result' => $results ]), function  ($items) { return is_array($items) || $items instanceof \Traversable; });
        if ($eventResults->stopped()) {
            $results = $eventResults->last();
        }

        return $results;
    }
}