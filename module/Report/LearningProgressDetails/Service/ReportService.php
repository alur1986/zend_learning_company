<?php

namespace Report\LearningProgressDetails\Service;

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

        $siteId = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $activityIds = array_key_exists('activity_id', $filter) ? (array) $filter['activity_id'] : null;
        $groupIds = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $showFrom = array_key_exists('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
        $showTo = array_key_exists('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
        $allDates = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $filterByDates = ($showFrom || $showTo) ? true : false;
        $trackingStatus = array_key_exists('tracking_status', $filter) ? $filter['tracking_status'] : null;
        $learnerStatus = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;
        $activityStatus = []; // [ 'active', 'migrated' ];
        $distributionStatus = [
            'new',
            'active',
            'approved',
            'inactive',
            'expired'
        ];

        // if there are no activity IDs selected, throw an error
        $results = [];
        $params = [];

        // create the DQL
        $dql = [];
        $dql[] = "SELECT
                	partial distribution.{distributionId} ,
                	trackerActivity.id AS tracker_id,
			        trackerActivity.score AS tracking_score,
			        trackerActivity.startedOn AS tracking_started,
			        trackerActivity.completedOn AS tracking_completed,
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


                	distribution.distributionDate AS distribution_date,
        			distribution.expiryDate AS expiry_date,

                	activity.activityId AS activity_id,
                	activity.activityType AS activity_type,
                	activity.title AS activity_title,
                	activity.keywords AS activity_keyword,
                	activity.code AS activity_code,
                	activity.version AS activity_version,
                	activity.cpd AS activity_cpd,
                	activity.duration AS activity_duration,
                	activity.directCost AS activity_direct_cost,
                	activity.indirectCost AS activity_indirect_cost,
                	category.term AS activity_category,

                	learner.userId AS learner_id,
                	learner.firstName AS learner_first_name,
                	learner.lastName AS learner_last_name,
                	CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                	learner.telephone AS learner_telephone,
                	learner.mobileNumber AS learner_mobile_number,
                	learner.email AS learner_email,
                	learner.streetAddress AS learner_street_address,
			      	learner.suburb AS learner_suburb,
			      	learner.postcode AS learner_postcode,
			      	learner.state AS learner_state,
			      	learner.country AS learner_country,
                	learner.gender AS learner_gender,
                	learner.agent AS agent_code,
                	learner.cpdId AS learner_cpd_id,
                	learner.cpdNumber AS learner_cpd_number,
                	learner.status AS learner_status,

					employment.employmentId AS employment_id,
					employment.employmentType AS employment_type,
					employment.position AS employment_position,
					employment.startDate AS employment_start_date,
					employment.endDate AS employment_end_date,
					employment.costCentre AS employment_cost_centre,
					employment.manager AS employment_manager,
					employment.location AS employment_location,
                	groups.name AS group_name,

                	site.siteId AS site_id,
                	site.name AS site_name
                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerActivity trackerActivity
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN activity.category category
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.site site
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                WHERE site.siteId = :siteId";

        $params['siteId'] = $siteId;

        // activity status
        $dql[] = "AND activity.status IN (:activityStatus) ";
        if ($activityStatus) {
            $params['activityStatus'] = $activityStatus;
        }
        else{
            $params['activityStatus'] = ['active','inactive'];
        }


        // distribution status
        if ($distributionStatus) {
            $dql[] = "AND distribution.status IN (:distributionStatus) ";
            $params['distributionStatus'] = $distributionStatus;
        }

        // if activity IDs is provided, filter results by activity IDs
        if ($activityIds) {
            $dql[] = "AND activity.activityId IN (:activityId)";
            $params['activityId'] = $activityIds;
        }

        // if group IDs is provided, filter results by group IDs
        if ($groupIds) {
            $dql[] = "AND groups.groupId IN (:groupId)";
            $params['groupId'] = $groupIds;
        }

        // learner IDs
        if ($learnerIds) {
            $dql[] = "AND learner.userId IN (:learnerId) ";
            $params['learnerId'] = $learnerIds;
        }

        // if learner status is defined, filter results by learner status
        $dql[] = "AND learner.status IN (:learnerStatus)";
        // learner status
        if ($learnerStatus) {
            $params['learnerStatus'] = $learnerStatus;
        }
        else{
            $params['learnerStatus'] = ['active','inactive','new'];
        }

        // tracking and distribution status
        if ($trackingStatus) {
            $dql[] = "AND (";
            $dql[] = "trackerActivity.completionStatus IN (:trackingStatus)";
            $params['trackingStatus'] = $trackingStatus;

            // completed
            if (in_array('completed', $trackingStatus) || in_array('complete', $trackingStatus)) {
                $dql[] = "OR trackerActivity.completionStatus IN (:completedTrackingStatus)";
                $params['completedTrackingStatus'] = [
                    'completed',
                    'complete'
                ];
            }

            // not attempted
            if (in_array('not-attempted', $trackingStatus)) {
                $dql[] = 'OR trackerActivity.completionStatus IS NULL OR trackerActivity.completionStatus = :notAttemptedTrackingStatus';
                $params['notAttemptedTrackingStatus'] = 'not-attempted';
            }
            $dql[] = ")";
        }

        // date range
        if ($filterByDates) {
            $dateFilter = [];

            // if tracking status is set, filter dates based on the selected tracking status
            if ($trackingStatus) {
                // show based on completion date
                if (array_intersect([
                    'completed',
                    'passed'
                ], $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "trackerActivity.completedOn >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "trackerActivity.completedOn <= :showTo" : '');
                }

                // show based on distribution date
                if (in_array('not-attempted', $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "distribution.distributionDate <= :showTo" : '');
                }

                // show based on tracker started date
                if (array_intersect([
                    'failed',
                    'incomplete'
                ], $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "trackerActivity.lastAccessed >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "trackerActivity.lastAccessed <= :showTo" : '');
                }
            }

            // use the distribution date by default
            else {
                $dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "distribution.distributionDate <= :showTo" : '');
            }

            if ($dateFilter) {
                $dql[] = sprintf(" AND (%s) ", implode(" OR ", $dateFilter));
                $showFrom ? $params['showFrom'] = $showFrom : null;
                $showTo ? $params['showTo'] = $showTo : null;
            }
        }

        // group by
        $dql[] = "GROUP BY distribution.distributionId";

        // order by
        $dql[] = "ORDER BY activity.title ASC, learner.firstName ASC, learner.lastName ASC, tracking_status_level DESC";

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

        $array  = [];
        $params = [];
        $repository = $this->agentRepository();
        foreach ($results as $result) {
            $agentName = null;
            if (isset($result['agent_code']) && strlen($result['agent_code'])) {
                // create query
                $qb = $repository->createQueryBuilder('agent')
                    ->leftJoin('agent.site', 'site')
                    ->select('agent')
                    ->where('agent.code = :code')
                    ->andWhere('site.siteId = :siteId')
                    ->setParameter('code', $result['agent_code'])
                    ->setParameter('siteId', $siteId);

                // execute query
                $agent = $repository->fetchOne($qb);
                $agentName = $agent['name'];
            }
            $result['agent_name'] = $agentName;
            $array[] = $result;
        }

    //    return $results;
        return $array;
    }

    /**
     * Get the agentt doctrine repository
     *
     * @return \Savvecentral\Entity\Agent
     */
    public function agentRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Agent');
    }
}
