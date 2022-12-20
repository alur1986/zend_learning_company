<?php

namespace Report\LearningProgressSummary\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
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
        $entityManager = $this->getEntityManager();

        $siteId = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $activityIds = array_key_exists('activity_id', $filter) ? (array) $filter['activity_id'] : [];
        $groupIds = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : [];
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : [];
        $showFrom = array_key_exists('show_from', $filter) ? new \DateTime(date('Y-m-d', strtotime($filter['show_from']))) : null;
        $showTo = array_key_exists('show_to', $filter) ? new \DateTime($filter['show_to']) : null;
        $allDates = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $trackingStatus = array_key_exists('tracking_status', $filter) ? $filter['tracking_status'] : [];
        $learnerStatus = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : [];
        $activityStatus = array_key_exists('activity_status', $filter) ? (array) $filter['activity_status'] : [];
        $distributionStatus = array_key_exists('distribution_status', $filter) ? (array) $filter['distribution_status'] : [ 'new', 'active', 'pending', 'enrolled', 'approved', 'inactive', 'expired' ];

		// if there are no activity IDs selected, throw an error
//        if (!$activityIds) {
//            throw new Exception\InvalidArgumentException(sprintf('Cannot execute the report. There are no learning activities selected.'), 500);
//        }

        $dql = [];
        $where = [];
        $joins = [];
        $params = [];
        $results = [];

        // create query
        $dql[] = "SELECT
                activity.activityId AS activity_id,
                activity.activityType AS activity_type,
                activity.title AS activity_title,
                activity.status AS activity_status,
                site.siteId AS site_id,
                site.name AS site_name,

                SUM(CASE WHEN distribution.status IS NOT NULL THEN 1 ELSE 0 END) AS num_learners_allocated,
                SUM(CASE WHEN (distribution.expiryDate IS NOT NULL AND distribution.expiryDate < CURRENT_TIMESTAMP()) OR distribution.status = 'expired' THEN 1 ELSE 0 END) AS num_learners_expired,
                SUM(CASE WHEN trackerActivity.completionStatus IS NOT NULL THEN 1 ELSE 0 END) AS num_learners_attempted,
                SUM(CASE WHEN trackerActivity.completionStatus IS NULL OR trackerActivity.completionStatus = 'not-attempted' THEN 1 ELSE 0 END) AS num_learners_not_attempted,
                SUM(CASE WHEN trackerActivity.completionStatus = 'incomplete' THEN 1 ELSE 0 END) AS num_learners_incomplete,
                SUM(CASE WHEN trackerActivity.completionStatus IN ('completed', 'complete') THEN 1 ELSE 0 END) AS num_learners_completed,
                SUM(CASE WHEN trackerActivity.completionStatus = 'passed' THEN 1 ELSE 0 END) AS num_learners_passed,
                SUM(CASE WHEN trackerActivity.completionStatus = 'failed' THEN 1 ELSE 0 END) AS num_learners_failed

                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN distribution.trackerActivity trackerActivity
                LEFT JOIN learner.site site";

        // create the WHERE clauses
        $where[] = "site.siteId = :siteId";
        $params['siteId'] = $siteId;

        // filter by distribution status
        if ($distributionStatus) {
            $where[] = "distribution.status IN (:distributionStatus)";
            $params['distributionStatus'] = $distributionStatus;
        }

        // filter by activity IDs
        if ($activityIds) {
            $where[] = "activity.activityId IN (:activityId) ";
            $params['activityId'] = $activityIds;
        }

        //For group admins there needs to be more filtering based on role
        $authorization = $this->getServiceLocator()->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();
        // current role level
        $level = $role['level'] ? $role['level']['id'] : 0;
        // if current logged in user's role is platform-admin (LEVEL_5), then show only the learners within that platform
        if ($level == \Authorization\Stdlib\Authorization::LEVEL_3 && !$groupIds) {
            $groupIds = $entityManager->createQuery (
                "SELECT groups.groupId
                FROM Savvecentral\\Entity\\Groups groups
                LEFT JOIN groups.groupLearners groupLearners
                WHERE groups.status IN ('active','new','inactive') AND groupLearners.learner  = :learnerId AND groupLearners.role = 'admin'")
                ->setParameter ('learnerId', $this->routeMatch ()->getParam ('user_id'))
                ->getArrayResult();
            $groupIds = array_map ('current', $groupIds);
        }

        // filter by group IDs
        if ($groupIds) {
            $joins[] = "LEFT JOIN learner.groupLearners groupLearners";
            $joins[] = "LEFT JOIN groupLearners.group groups";
            $where[] = "groups.groupId IN (:groupId)";
            $params['groupId'] = $groupIds;
        }

        // if learner status is defined, filter results by learner status
        $where[] = "learner.status IN (:learnerStatus)";
        // learner status
        if ($learnerStatus) {
            $params['learnerStatus'] = $learnerStatus;
        }
        else{
            $params['learnerStatus'] = ['active','inactive','new'];
        }

        // filter by showFrom and showTo dates
        if ($showFrom || $showTo) {
            $dateFilter = [];

            // if tracking status is set, filter dates based on the selected tracking status
            if ($trackingStatus) {
                // show based on completion date
                if (array_intersect([ 'completed', 'passed'], $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "trackerActivity.completedOn >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "trackerActivity.completedOn <= :showTo" : '');
                }

                // show based on distribution date
                if (in_array('not-attempted', $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "distribution.distributionDate <= :showTo" : '');
                }

                // show based on tracker started date
                if (array_intersect([ 'incomplete', 'failed'  ], $trackingStatus)) {
                    $dateFilter[] = ($showFrom ? "trackerActivity.lastAccessed >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "trackerActivity.lastAccessed <= :showTo" : '');
                }
            }

            // use the distribution date by default
            else {
                $dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '') . ($showTo ? "distribution.distributionDate <= :showTo" : '');
            }

            if ($dateFilter) {
                $where[] = "(" . implode(" OR ", $dateFilter) . ")";
                $showFrom ? $params['showFrom'] = $showFrom : null;
                $showTo ? $params['showTo'] = $showTo : null;
            }
        }

        // filter by activity status
        $where[] = "activity.status IN (:activityStatus) ";
        if ($activityStatus) {
            $params['activityStatus'] = $activityStatus;
        }
        else{
            $params['activityStatus'] = ['active','inactive'];
        }

        // combine all the JOIN clauses
        $dql[] = implode(' ', $joins);

        // combine all the WHERE clauses
        $dql[] = "WHERE " . implode(' AND ', $where);

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // order by
        $dql[] = "ORDER BY activity.title ASC, activity.activityId ASC";

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