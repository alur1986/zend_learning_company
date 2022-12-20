<?php

namespace Report\IndividualLearning\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savve\Stdlib;

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
        $orderBy = array_key_exists('order_by', $filter) ? (array) $filter['order_by'] : [
            'learner.firstName',
            'learner.lastName',
            'activity.title'
        ];

        $results = [];
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
                	(CASE
                		WHEN trackerActivity.completionStatus IN ('not-attempted') OR trackerActivity.completionStatus IS NULL THEN 'not-attempted'
                		WHEN trackerActivity.completionStatus IN ('completed', 'complete') THEN 'completed'
                		ELSE trackerActivity.completionStatus
                	END) AS tracking_status,
			        trackerActivity.startedOn AS tracking_started,
			        trackerActivity.lastAccessed AS tracking_last_accessed,
			        trackerActivity.completedOn AS tracking_completed,
                	distribution.distributionDate AS distribution_date,
                	distribution.expiryDate AS expiry_date

                FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN learner.site site
                LEFT JOIN distribution.trackerActivity trackerActivity
                WHERE site.siteId = :siteId
                AND distribution.status IN (:distributionStatus)
                ";

        $params['siteId'] = $siteId;
        $params['distributionStatus'] = ['active','approved','new','expired'];

        // if activity IDs is provided, filter results by activity IDs
        if ($activityIds) {
            $dql[] = "AND activity.activityId IN (:activityId)";
            $params['activityId'] = $activityIds;
        }
        else{
            $dql[] = "AND activity.status NOT IN (:activityStatus)";
            $params['activityStatus'] = ['deleted'];
        }
        // learner IDs
        if ($learnerIds) {
            $dql[] = "AND learner.userId IN (:learnerId)";
            $params['learnerId'] = $learnerIds;
        }

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
            $dql[] = "AND (" . "trackerActivity.completionStatus IN (:trackingStatus)" . (in_array('not-attempted', $trackingStatus) ? ' OR trackerActivity.completionStatus IS NULL' : '') . ")";
            $params['trackingStatus'] = $trackingStatus;
        }

        // date range
        if ($filterByDates) {
            $dateFilter = [];

            // if tracking status is set, filter dates based on the selected tracking status
            if ($trackingStatus) {
                // show based on completion date
                if (array_intersect([ 'completed', 'passed' ], $trackingStatus)) {
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
                $dql[] = sprintf("AND (%s)", implode(" OR ", $dateFilter));
                $showFrom ? $params['showFrom'] = $showFrom : null;
                $showTo ? $params['showTo'] = $showTo : null;
            }
        }

        // show only completed
        //$dql[] = "AND trackerActivity.completionStatus IN ('completed', 'complete')";


        // group by
        $dql[] = "GROUP BY distribution.distributionId";

        // order by
        if ($orderBy) {
            $orderBy = implode(", ", $orderBy);
            $dql[] = sprintf("ORDER BY %s", $orderBy);
        }

        // execute query
        $dql = implode(' ', $dql);
        $query = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)));
        $results = $query->getScalarResult();

        // \Zend\Debug\Debug::dump(__METHOD__ . ' ' . __LINE__); \Zend\Debug\Debug::dump($query->getDQL()); \Zend\Debug\Debug::dump($query->getSql()); \Zend\Debug\Debug::dump($query->getParameters());

        // trigger event listeners
        $eventManager = $this->getEventManager();
        $eventResults = $eventManager->trigger(new Event(Event::EVENT_REPORT_POST, $this, [ 'result' => $results ]), function  ($items) { return is_array($items) || $items instanceof \Traversable; });
        if ($eventResults->stopped()) {
            $results = $eventResults->last();
        }

        if (count($results) === 0) {
            return [];
        }

        return $results;
    }

    public function convertReportToCsv(array $results, string $name)
    {
        $results = $this->preConvertFormat($results);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name . '.csv');
        header('Pragma: no-cache');
        header("Expires: 0");
        $outStream = fopen("php://output", "wb");

        foreach ($results as $key => $row) {
            fputcsv($outStream, $row);
            $results->offsetUnset($key);
        }
        fclose($outStream);
        exit(0);
    }

    public function convertReportToExcel(array $results, string $name)
    {
        $results = $this->preConvertFormat($results);
        $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF));

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=' . $name . '.xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: no-cache');
        header("Expires: 0");

        $outStream = fopen("php://output", "wb");
        // Add BOM to fix UTF-8 in Excel
        fputs($outStream, $bom);
        foreach ($results as $row) {
            fputcsv($outStream, $row, ",");
            unset($row);
        }
        fclose($outStream);
        exit(0);
    }

    private function preConvertFormat(array $results): \SplFixedArray
    {
        $title = [
            'Learner Name',
            'Activity Title',
            'CPD points',
            'Distributed Date',
            'Date First Accessed',
            'Date Last Accessed',
            'Completion Date',
            'Completion Status',
            'Score',
        ];

        $data = new \SplFixedArray(count($results) + 1);
        $data->offsetSet(0, $title);
        foreach ($results as $key => $item) {
            $data->offsetSet($key + 1, [
                trim($item['learner_name']),
                trim($item['activity_title']),
                trim($item['activity_cpd']),
                trim($item['distribution_date']),
                trim($item['tracking_started']),
                trim($item['tracking_last_accessed']),
                trim($item['tracking_completed']),
                ucwords($item['tracking_status']),
                $item['tracking_score'],
            ]);

            unset($item);
        }

        return $data;

    }
}