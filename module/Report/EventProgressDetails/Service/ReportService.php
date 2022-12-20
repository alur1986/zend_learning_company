<?php

namespace Report\EventProgressDetails\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ReportService extends AbstractService {

	/**
	 * Create the report
	 *
	 * @param array $filter Array of filters to use to generate the report
	 *
	 * @return array $report Array containing the data of the report
	 */
	public function report (array $filter)
	{
		/* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
		$entityManager = $this->getEntityManager ();

		$siteId = array_key_exists ('site_id', $filter) ? $filter['site_id'] : null;
		$activityIds = array_key_exists ('activity_id', $filter) ? (array)$filter['activity_id'] : null;
		$eventIds = array_key_exists ('event_id', $filter) ? (array)$filter['event_id'] : null;
		$eventStatus = array_key_exists ('event_status', $filter)
			? (array)$filter['event_status']
			: [
				'new', 'active', 'enabled', 'inactive', 'disabled', 'finished', 'expired'
			];
		$groupIds = array_key_exists ('group_id', $filter) ? (array)$filter['group_id'] : null;
		$learnerIds = array_key_exists ('learner_id', $filter) ? (array)$filter['learner_id'] : null;
		$showFrom = array_key_exists ('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
		$showTo = array_key_exists ('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
		$allDates = array_key_exists ('all_dates', $filter) ? (bool)$filter['all_dates'] : null;
		$filterByDates = ($showFrom || $showTo) ? true : false;
		$trackingStatus = array_key_exists ('tracking_status', $filter) ? (array)$filter['tracking_status'] : null;
		$learnerStatus = array_key_exists ('learner_status', $filter) ? (array)$filter['learner_status'] : null;
		$activityStatus = array_key_exists ('activity_status', $filter) ? (array)$filter['activity_status'] : ['active', 'migrated'];
		$distributionStatus = array_key_exists ('distribution_status', $filter)
			? (array)$filter['distribution_status']
			: [
				'new', 'active', 'pending', 'enrolled', 'approved', 'inactive', 'expired'
			];

		$dql = [];
		$where = [];
		$join = [];
		$params = [];
		$results = [];

		// create query
		$dql[]
			= "SELECT
                	partial eventDistribution.{id},
                	eventDistribution.id AS event_distribution_id,
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
                	learner.status AS learner_status,
					employment.employmentId AS employment_id,
					employment.employmentType AS employment_type,
					employment.position AS employment_position,
					employment.startDate AS employment_start_date,
					employment.endDate AS employment_end_date,
					employment.costCentre AS employment_cost_centre,
					employment.manager AS employment_manager,
                	groups.name AS group_name,
                	site.siteId AS site_id,
                	site.name AS site_name,
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
                	event.eventId AS event_id,
                	event.title AS event_title,
                	event.description AS event_description,
                	event.startDate AS event_start_date,
                	event.endDate AS event_end_date,
                	trackerEvent.score AS tracking_score,
                	trackerEvent.lastAccessed AS tracking_date
                	,(CASE
                		WHEN trackerEvent.completionStatus IN ('not-attempted') OR trackerEvent.completionStatus IS NULL THEN 'not-attempted'
                		WHEN trackerEvent.completionStatus IN ('completed', 'complete') THEN 'completed'
                		ELSE trackerEvent.completionStatus
                	END) AS tracking_status
                FROM Savvecentral\Entity\EventDistribution eventDistribution
                LEFT JOIN eventDistribution.learner learner
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                LEFT JOIN eventDistribution.event event
                LEFT JOIN eventDistribution.trackerEvents trackerEvent
                LEFT JOIN event.activity activity
                LEFT JOIN activity.distribution distribution";

		// create where clauses
		$where[] = "site.siteId = :siteId";
		$params['siteId'] = $siteId;

		// if activity IDs is provided, filter results by activity IDs
		if ($activityIds) {
			$where[] = "activity.activityId IN (:activityId)";
			$params['activityId'] = $activityIds;
		}

		// event IDs
		if ($eventIds) {
			$where[] = "event.eventId IN (:eventId)";
			$params['eventId'] = (array)$eventIds;
		}

		// event status
		$where[] = "event.status IN (:eventStatus)";
		if ($eventStatus) {
			$params['eventStatus'] = $eventStatus;
		}
		else{
			$params['eventStatus'] =['enabled','disabled','new','finished'];
		}

		// distribution status
		if ($distributionStatus) {
			$where[] = "distribution.status IN (:distributionStatus)";
			$params['distributionStatus'] = $distributionStatus;
		}

		// group IDs
		if ($groupIds) {
			$where[] = "groups.groupId IN (:groupId)";
			$params['groupId'] = $groupIds;
		}

		// learner IDs
		if ($learnerIds) {
			$where[] = "learner.userId IN (:learnerId)";
			$params['learnerId'] = $learnerIds;
		}



		// tracking and distribution status
		if ($trackingStatus) {
			$trackingFilter = [];

			// completed events
			if (array_intersect (['completed', 'passed', 'failed'], $trackingStatus)) {
				$trackingFilter[] = "trackerEvent.completionStatus IN ('completed', 'complete', 'passed', 'failed')";
			}

			// passed events
			if (in_array ('passed', $trackingStatus)) {
				$trackingFilter[] = "trackerEvent.completionStatus = 'passed'";
			}

			// failed events
			if (in_array ('failed', $trackingStatus)) {
				$trackingFilter[] = "trackerEvent.completionStatus = 'failed'";
			}

			// incomplete events
			if (in_array ('incomplete', $trackingStatus)) {
				$trackingFilter[] = "trackerEvent.completionStatus = 'incomplete'";
			}

			// not attempted
			if (in_array ('not-attempted', $trackingStatus)) {
				$trackingFilter[]
					= "(trackerEvent.completionStatus IS NULL OR trackerEvent.completionStatus = 'not-attempted') AND (distribution.status IS NOT NULL)";
			}

			// build the tracking WHERE sub-clause
			if ($trackingFilter) {
				$where[] = "(" . implode (' OR ', $trackingFilter) . ")";
			}
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
		// date range
		if ($filterByDates) {
			$dateFilter = [];

			// if tracking status is set, filter dates based on the selected tracking status
			if ($trackingStatus) {
				// show based on completion date
				if (array_intersect ([
										 'completed', 'passed'
									 ], $trackingStatus)) {
					$dateFilter[] = ($showFrom ? "trackerActivity.completedOn >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '')
						. ($showTo ? "trackerActivity.completedOn <= :showTo" : '');
				}

				// show based on distribution date
				if (in_array ('not-attempted', $trackingStatus)) {
					$dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '')
						. ($showTo ? "distribution.distributionDate <= :showTo" : '');
				}

				// show based on tracker started date
				if (array_intersect ([
										 'failed', 'incomplete'
									 ], $trackingStatus)) {
					$dateFilter[] = ($showFrom ? "trackerActivity.lastAccessed >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '')
						. ($showTo ? "trackerActivity.lastAccessed <= :showTo" : '');
				}
			}

			// use the distribution date by default
			else {
				$dateFilter[] = ($showFrom ? "distribution.distributionDate >= :showFrom" : '') . ($showFrom && $showTo ? " AND " : '')
					. ($showTo ? "distribution.distributionDate <= :showTo" : '');
			}

			if ($dateFilter) {
				$dql[] = sprintf (" AND (%s) ", implode (" OR ", $dateFilter));
				$showFrom ? $params['showFrom'] = $showFrom : null;
				$showTo ? $params['showTo'] = $showTo : null;
			}
		}
		// date range
		if ($filterByDates) {
			$result = [];
			if ($trackingStatus && array_intersect (['completed', 'passed', 'failed', 'incomplete'], $trackingStatus)) {
				$dateFilter = [];
				// showFrom
				if ($showFrom) {
					$dateFilter[] = "trackerEvent.lastAccessed >= :showFrom";
				}

				if ($showTo) {
					$dateFilter[] = "trackerEvent.lastAccessed <= :showTo";
				}
				$result[] = sprintf ("(%s) ", implode (" AND ", $dateFilter));
			}


			if (!$trackingStatus ||  !array_intersect (['completed', 'passed', 'failed', 'incomplete'], $trackingStatus)) {
				$dateFilter = [];
				// showFrom
				if ($showFrom) {
					$dateFilter[] = "event.startDate >= :showFrom";
				}

				if ($showTo) {
					$dateFilter[] = "event.endDate <= :showTo";
				}
				$result[] = sprintf ("(%s) ", implode (" AND ", $dateFilter));
			}


			if ($dateFilter) {
				$dql[] = sprintf (" AND (%s) ", implode (" OR ", $result));
				$showFrom ? $params['showFrom'] = $showFrom : null;
				$showTo ? $params['showTo'] = $showTo : null;
			}
		}


		// build the where clause
		if ($where) {
			$dql[] = "WHERE " . implode (" AND ", $where);
		}

		// group by
		$dql[] = "GROUP BY eventDistribution.id";

		// order by
		$dql[] = "ORDER BY event.title, learner.firstName, learner.lastName";

		// execute query
		$dql = implode (' ', $dql);
		$results = $entityManager->createQuery ($dql)->setParameters ($params)->useResultCache (true, (60 * 60
				* 15), md5 (Stdlib\StringUtils::dashed ($dql) . serialize ($params)))->getScalarResult ();

		// trigger event listeners
		$eventManager = $this->getEventManager ();
		$eventResults = $eventManager->trigger (new Event(Event::EVENT_REPORT_POST, $this, ['result' => $results]), function ($items) {
			return is_array ($items) || $items instanceof \Traversable;
		});
		if ($eventResults->stopped ()) {
			$results = $eventResults->last ();
		}

		return $results;
	}
}