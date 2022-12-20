<?php

namespace Report\LearningPlaylist\Service;

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
     * @return array|mixed  Array containing the data of the report
     * @throws \Exception
     */
    public function report (array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $siteId     = array_key_exists('site_id', $filter) ? $filter['site_id'] : null;
        $planIds    = array_key_exists('plan_id', $filter) ? (array) $filter['plan_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;

        $showFrom = array_key_exists('show_from', $filter) && $filter['show_from'] ? new \DateTime($filter['show_from']) : null;
        $showTo = array_key_exists('show_to', $filter) && $filter['show_from'] ? new \DateTime($filter['show_to']) : null;
        $allDates = array_key_exists('all_dates', $filter) ? (bool) $filter['all_dates'] : null;
        $filterByDates = ($showFrom || $showTo) ? true : false;

        $trackingStatus = array_key_exists('tracking_status', $filter) ? $filter['tracking_status'] : null;
        $learnerStatus  = array_key_exists('learner_status', $filter) ? $filter['learner_status'] : null;
        $aggregatedData = array_key_exists('aggregated_output', $filter) ? $filter['aggregated_output'] : 'no';
        $activityStatus = []; // [ 'active', 'migrated' ];
        $distributionStatus = [
            'new',
            'active',
            'approved',
            'inactive',
            'expired'
        ];

        // if there are no plan IDs selected, throw an error
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
                	learner.status AS learner_status,
                	
                	learner.agent AS agency_code,

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
                	site.name AS site_name,

                	plan.title as plan_title,
                	plan.planId as plan_id

                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerActivity trackerActivity
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.plan plan
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
        if ($planIds) {
            $dql[] = "AND plan.planId IN (:planIds)";
            $params['planIds'] = $planIds;
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

        if ($aggregatedData == 'yes') {

            // simplify the output so that only the playlists info is displayed
            // get all the courses first
            $courseActivityIds = array(); // store activity ID's that are part of a course
            $currentCourse = false;   // the current course being evaluated ( where activites within a course are concurrent in the iteration )

            // playlist container
            $playlists = array();
            foreach ($results as $item) {

                //    $course = $item['plan_title'];
                //    $courseId = $item['plan_id'];
                //    $completion = true;

                if (isset($playlists[$item['plan_id']]) && is_array($playlists[$item['plan_id']]) && count($playlists) >= 1) {

                //    $learnerFound = false;
                //    $learners = $playlists[$item['plan_id']]["learners"];
                //    foreach ($learners as $check) {
                //        if ($item['learner_id'] == $check['learner_id']) {
                 //           $learnerFound = true;
                 //       }
                 //   }
                 //   if ($learnerFound == false) {
                //        /** ToDo: we dont want to add a learner twice */
                //        $playlists[$item['plan_id']]["learners"][] = array(
                 //
                //       );
                 //   }
                    $playlists[$item['plan_id']]["activities"][] = array(
                        "activity_id"        => $item['activity_id'],
                        "activity_title"     => $item['activity_title'],
                        "activity_type"      => $item['activity_type'],
                        "activity_cpd"       => $item['activity_cpd'],
                        "tracking_score"     => $item['tracking_score'],
                        "tracking_started"   => $item['tracking_started'],
                        "tracking_completed" => $item['tracking_completed'],
                        "tracking_status"    => $item['tracking_status'],
                        "learner_id"         => $item['learner_id'],
                        "learner_name"       => $item['learner_name'],
                        "learner_first_name" => $item['learner_first_name'],
                        "learner_last_name"  => $item['learner_last_name'],
                        "learner_email"      => $item['learner_email'],
                        "agency_code"        => $item['agency_code'],
                        "learner_status"     => $item['learner_status'],
                        "distribution_date"  => $item['distribution_date'],
                    );

                } else {

                    $playlists[$item['plan_id']] = array(
                        "plan_title"        => $item['plan_title'],
                        "activities"        => array(
                            array(
                                "activity_id"        => $item['activity_id'],
                                "activity_title"     => $item['activity_title'],
                                "activity_type"      => $item['activity_type'],
                                "activity_cpd"       => $item['activity_cpd'],
                                "tracking_score"     => $item['tracking_score'],
                                "tracking_started"   => $item['tracking_started'],
                                "tracking_completed" => $item['tracking_completed'],
                                "tracking_status"    => $item['tracking_status'],
                                "learner_id"         => $item['learner_id'],
                                "learner_name"       => $item['learner_name'],
                                "learner_first_name" => $item['learner_first_name'],
                                "learner_last_name"  => $item['learner_last_name'],
                                "learner_email"      => $item['learner_email'],
                                "agency_code"        => $item['agency_code'],
                                "learner_status"     => $item['learner_status'],
                                "distribution_date" => $item['distribution_date'],
                            )
                        )
                    );
                }
            }

            // now analyse the playlist array and determine the learners for each group of activities
            $learners = array();
            foreach ($playlists as $planId => $playlist) {
                $activities = $playlist['activities'];

                // aggregate each learners` activities data together
                foreach ($activities as $activity) {
                    $learnerId = $activity['learner_id'];
                    if ($learners[$learnerId] && is_array($learners[$learnerId]) && count($learners) >= 1) {
                        $learners[$learnerId]['activities'][] = array(
                            "activity_id"        => $activity['activity_id'],
                            "activity_title"     => $activity['activity_title'],
                            "activity_type"      => $activity['activity_type'],
                            "activity_cpd"       => $activity['activity_cpd'],
                            "tracking_score"     => $activity['tracking_score'],
                            "tracking_started"   => $activity['tracking_started'],
                            "tracking_completed" => $activity['tracking_completed'],
                            "tracking_status"    => $activity['tracking_status']
                        );

                    } else {

                        $learners[$learnerId] = array(
                            "plan_id"            => $planId,
                            "plan_title"         => $playlist['plan_title'],
                            "distribution_date"  => $activity['distribution_date'],
                            "learner_id"         => $activity['learner_id'],
                            "learner_name"       => $activity['learner_name'],
                            "learner_first_name" => $activity['learner_first_name'],
                            "learner_last_name"  => $activity['learner_last_name'],
                            "learner_email"      => $activity['learner_email'],
                            "agency_code"        => $activity['agency_code'],
                            "learner_status"     => $activity['learner_status'],
                            "activities" => array(
                                array(
                                    "activity_id"        => $activity['activity_id'],
                                    "activity_title"     => $activity['activity_title'],
                                    "activity_type"      => $activity['activity_type'],
                                    "activity_cpd"       => $activity['activity_cpd'],
                                    "tracking_score"     => $activity['tracking_score'],
                                    "tracking_started"   => $activity['tracking_started'],
                                    "tracking_completed" => $activity['tracking_completed'],
                                    "tracking_status"    => $activity['tracking_status']
                                )
                            )
                        );
                    }
                }
            }

            // now analyse the learners activities and set the completion status for the playlist / course
            $courses = array();
            foreach ($learners as $learner) {

                $courseId         = $learner['plan_id']  ;
                $activities       = $learner['activities'];
                $completionStatus = 'completed';
                $completionCount  = 0;

                foreach ($activities as $activity) {
                    if ($activity['tracking_status'] == 'completed') {
                        $completionCount++;

                    } else {
                        // playlist / course has not been completed
                        $completionStatus = 'incomplete';
                    }
                }

                if (is_array($courses) && count($courses) >= 1) {
                    $courses[] = array(
                        "plan_id"            => $courseId,
                        "plan_title"         => $learner['plan_title'],
                        "distribution_date"  => $learner['distribution_date'],
                        "completion_status"  => $completionStatus,
                        "tracking_status"    => $completionStatus,
                        "completion_count"   => $completionCount,
                        "leaner_id"          => $learner['learner_id'],
                        "learner_name"       => $learner['learner_name'],
                        "learner_first_name" => $learner['learner_first_name'],
                        "learner_last_name"  => $learner['learner_last_name'],
                        "learner_email"      => $learner['learner_email'],
                        "learner_status"     => $learner['learner_status'],
                        "agency_code"        => $learner['agency_code'],
                        "activity_type"      => 'playlist'
                    );

                } else {
                    $courses = array(
                        array(
                            "plan_id"            => $courseId,
                            "plan_title"         => $learner['plan_title'],
                            "distribution_date"  => $learner['distribution_date'],
                            "completion_status"  => $completionStatus,
                            "tracking_status"    => $completionStatus,
                            "completion_count"   => $completionCount,
                            "leaner_id"          => $learner['learner_id'],
                            "learner_name"       => $learner['learner_name'],
                            "learner_first_name" => $learner['learner_first_name'],
                            "learner_last_name"  => $learner['learner_last_name'],
                            "learner_email"      => $learner['learner_email'],
                            "learner_status"     => $learner['learner_status'],
                            "agency_code"        => $learner['agency_code'],
                            "activity_type"      => 'playlist'
                        )
                    );
                }
            }

            if (count($courses) >= 1) {
                $results = $courses;
            }
        }

        return $results;
    }

    public function getDistributedLearners($planId)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $repository = $entityManager->getRepository('Savvecentral\Entity\Distribution');

        $dql[] = "SELECT distribution, learner
            FROM Savvecentral\Entity\Distribution distribution
            LEFT JOIN distribution.plan plan
            LEFT JOIN distribution.learner learner
            WHERE plan.planId = :planId
            GROUP BY learner.userId";

        $params['planId'] = $planId;

        $distributions = $repository->fetchCollection($dql, $params);

        return $distributions;

    }


}