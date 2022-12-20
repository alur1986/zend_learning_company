<?php

namespace Report\EventProgressSummary\Service;

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
        $eventIds = array_key_exists('event_id', $filter) ? (array) $filter['event_id'] : null;
        $groupIds = array_key_exists('group_id', $filter) ? (array) $filter['group_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $showFrom = array_key_exists('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
        $showTo = array_key_exists('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
        $allDates = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $filterByDates = ($showFrom || $showTo) ? true : false;
        $trackingStatus = array_key_exists('tracking_status', $filter) ? $filter['tracking_status'] : null;
        $learnerStatus = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;
        $activityStatus = [ 'active', 'migrated' ];
        $distributionStatus = [ 'new', 'active', 'pending', 'enrolled', 'approved', 'inactive', 'expired' ];

        $results = [];
        $params = [];
        $joins = [];
        $where = [];
        $dqlparts = [];


        $dqlparts[] = "SELECT
                       eventDistribution,
                       partial event.{eventId},
                       event.eventId AS event_id,
                       event.title AS event_title,
                       event.description AS event_description,
                       event.capacity AS event_capacity,
                       event.startDate AS event_start_date,
                       event.endDate AS event_end_date,
                       event.status AS event_status,
                       activity.activityId AS activity_id,
                       activity.activityType AS activity_type,
                       activity.title AS activity_title,
                       site.siteId AS site_id,
                       site.name AS site_name,
        ";
        $dqlparts [] = "(SELECT COUNT(evtVenues.id)
                		FROM Savvecentral\Entity\Event evt1
		                LEFT JOIN evt1.eventVenues evtVenues
		                WHERE evt1.eventId = event.eventId) AS num_venues,";

        $dqlparts [] = "(SELECT COUNT(evtVendors.id)
		                FROM Savvecentral\Entity\Event evt2
		                LEFT JOIN evt2.eventVendors evtVendors
		                WHERE evt2.eventId = event.eventId) AS num_vendors,";

        $dqlparts [] = "(SELECT COUNT(evtAssessors.id)
		                FROM Savvecentral\Entity\Event evt3
		                LEFT JOIN evt3.eventAssessors evtAssessors
		                WHERE evt3.eventId = event.eventId) AS num_assessors,";

        $dqlparts [] = "(SELECT COUNT(evtFacilitators.id)
		                FROM Savvecentral\Entity\Event evt4
		                LEFT JOIN evt4.eventFacilitators evtFacilitators
		                WHERE evt4.eventId = event.eventId) AS num_facilitators,";

        $dqlparts[] = "SUM(CASE WHEN eventDistribution.status IS NOT NULL THEN 1 ELSE 0 END) AS num_learners_allocated,";
        $dqlparts[] = "SUM(CASE WHEN trackerEvents.completionStatus IS NOT NULL THEN 1 ELSE 0 END) AS num_learners_attempted,";
        $dqlparts[]=  "SUM(CASE WHEN trackerEvents.completionStatus IS NULL OR trackerEvents.completionStatus = 'not-attempted' THEN 1 ELSE 0 END) AS num_learners_not_attempted,";
        $dqlparts[]=  "SUM(CASE WHEN trackerEvents.completionStatus = 'incomplete' THEN 1 ELSE 0 END) AS num_learners_incomplete,";
        $dqlparts[]=  "SUM(CASE WHEN trackerEvents.completionStatus  IN ('completed', 'complete') THEN 1 ELSE 0 END) AS num_learners_completed,";
        $dqlparts[]=  "SUM(CASE WHEN trackerEvents.completionStatus = 'passed' THEN 1 ELSE 0 END) AS num_learners_passed,";
        $dqlparts[]=  "SUM(CASE WHEN trackerEvents.completionStatus = 'failed' THEN 1 ELSE 0 END) AS num_learners_failed";

        $dqlparts[]= " FROM Savvecentral\Entity\EventDistribution eventDistribution";
        $dql[] = implode(" ",$dqlparts);

        $joins[]= "LEFT JOIN eventDistribution.event event";
        $joins[]= "LEFT JOIN event.activity activity";
        $joins[]= "LEFT JOIN activity.site site";
        $joins[]= "LEFT JOIN eventDistribution.learner learner";
        $joins[]= "LEFT JOIN eventDistribution.trackerEvents trackerEvents";


        // create the WHERE clauses
        $where[] = "site.siteId = :siteId";
        $params['siteId'] = $siteId;

        // if learner status is defined, filter results by learner status
        $where[] = "learner.status IN (:learnerStatus)";
        // learner status
        if ($learnerStatus) {
            $params['learnerStatus'] = $learnerStatus;
        }
        else{
            $params['learnerStatus'] = ['active','inactive','new'];
        }

        // learner IDs
        if ($learnerIds) {
            $where[] =  " learner.userId IN (:learnerId) ";
            $params['learnerId'] = $learnerIds;
        }

        // filter by distribution status
        if ($distributionStatus) {
            $where[] = "eventDistribution.status IN (:distributionStatus)";
            $params['distributionStatus'] = $distributionStatus;
        }

        //filter by group ids
        if ($groupIds) {
            $joins[] = "LEFT JOIN learner.groupLearners groupLearners";
            $joins[] = "LEFT JOIN groupLearners.group groups";
            $where[] = "groups.groupId IN (:groupId)";
            $params['groupId'] = $groupIds;
        }

        //filter by event status
        $where[] = " event.status IN (:eventStatus) ";
        $params['eventStatus'] =['enabled','disabled','new','finished'];

        // event IDs
        if ($eventIds) {
            $where[] =  " event.eventId IN (:eventId) ";
            $params['eventId'] = (array) $eventIds;
        }

        // tracking and distribution status
        if ($trackingStatus) {
            $where[] =  " ("
                . "trackerEvent.completionStatus IN (:trackingStatus)"
                . (in_array('not-attempted', $trackingStatus) ? ' OR trackerEvent.completionStatus IS NULL' : '')
                . ") ";
            $params['trackingStatus'] = $trackingStatus;
        }

        // date range
        if ($filterByDates) {
            $where[] =  " ("
                . ($showFrom ? "event.startDate >= :showFrom" : '')
                . ($showFrom && $showTo ? " AND " : '')
                . ($showTo ? "event.endDate <= :showTo" : '')
                . ") ";
            $showFrom ? $params['showFrom'] = $showFrom : null;
            $showTo ? $params['showTo'] = $showTo : null;
        }


        // combine all the JOIN clauses
        $dql[] = implode(' ', $joins);

        // combine all the WHERE clauses
        $dql[] = "WHERE " . implode(' AND ', $where);

        // group by
        $dql[] = " GROUP BY event.eventId";

        // order by
        $dql[]= " ORDER BY event.title ASC";

        // execute query
        $dql = implode(' ', $dql);



        // execute query
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