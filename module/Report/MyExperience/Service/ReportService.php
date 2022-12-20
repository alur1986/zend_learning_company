<?php

namespace Report\MyExperience\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use TinCan;

class ReportService extends AbstractService
{
    /**
     * Fetch xAPI activities for the directory listing
     *
     * @param $siteId
     * @param $learnerId
     * @return array
     */
    public function getActivities($siteId, $learnerId)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT
            distribution.distributionId AS distribution_id,
            activity.activityId AS activity_id,
            activity.title AS activity_title,
            activity.cpd AS activity_cpd,
            (CASE
                WHEN trackerActivity.completionStatus IN ('not-attempted') OR trackerActivity.completionStatus IS NULL THEN 'not-attempted'
                WHEN trackerActivity.completionStatus IN ('completed', 'complete') THEN 'completed'
                ELSE trackerActivity.completionStatus
            END) AS tracking_status,
            trackerTincanItem.totalTime as total_time,
            trackerActivity.startedOn AS tracking_started,
            trackerActivity.completedOn AS tracking_completed

            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN learner.site site
                LEFT JOIN distribution.trackerActivity trackerActivity
                LEFT JOIN distribution.trackerTincanItem trackerTincanItem
                WHERE site.siteId = :siteId
                AND activity.activityType IN (:activityType)
                AND activity.status IN (:activityStatus)
                AND distribution.status IN (:distributionStatus)";

        // site.site_id
        $params['siteId'] = $siteId;

        //learning_activity.status
        $params['activityStatus'] = ['active','inactive','new']; //@todo : Should inactive come up ?

        // activcity type (tincan only)
        $params['activityType'] = ['tincan'];

        // distribution
        $params['distributionStatus'] = ['active','new','expired','approved'];

        // learner IDs
        $dql[] = "AND learner.userId IN (:learnerId)";
        $params['learnerId'] = $learnerId;

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // order by
        if (isset($orderBy)) {
            $orderBy = implode(", ", $orderBy);
            $dql[] = sprintf("ORDER BY %s", $orderBy);
        }

        // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        return $results;

    }

    /**
     * Determine and return the LRS credentials
     *
     * @param $optionService
     * @return array
     */
    private function getLrsCredentials( $optionService ) {

        // get the site -  we need the LRS username and password from ther Site entity
        $site = $this->getServiceLocator()->get('Site\Entity');

        // return an array
        $arr = array();

        // $lrsUsername: use options default if site value is empty
        if (empty($site['lrs_username'])) {
            $arr['lrsUsername'] = $optionService->getLrsUser();
        } else {
            $arr['lrsUsername'] = $site['lrs_username'];
        }

        // $lrsPassword: use options default if site value is empty
        if (empty($site['lrs_password'])) {
            $arr['lrsPassword'] = $optionService->getLrsPassword();
        } else {
            $arr['lrsPassword'] = $site['lrs_password'];
        }

        // $lrsUrl: use options default if site value is empty
        if (empty($site['lrs_url'])) {
            $arr['lrsUrl'] = $optionService->getLrsUrl();
        } else {
            $arr['lrsUrl'] = $site['lrs_url'];
        }

        return $arr;
    }

    /**
     * Fetch xAPI interaction statements from the LRS
     *
     * @param $distributionId
     * @param $optionService
     * @param bool $learner
     * @return bool
     */
    public function getInteractions($distributionId, $optionService, $learner = false)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();
        $statements    = array();

        $dql = [];
        $dql[] = "SELECT distribution, trackerTincanItem, tincanItem, activity
            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerTincanItem trackerTincanItem
                LEFT JOIN trackerTincanItem.item tincanItem
                LEFT JOIN distribution.activity activity
                WHERE distribution.distributionId = :distributionId";

        // distribution ID
        $params['distributionId'] = $distributionId;

        // execute query
        $dql = implode(' ', $dql);
        $result = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // get the homepage associated with the current activity
        if ($result[0]['tincanItem_itemHomepage']) {
            $homePage = $result[0]['tincanItem_itemHomepage'];
        } else {
            $homePage =  $optionService->getHomepage();
        }

        // get the IRI of the current Activity
        if ($result[0]['tincanItem_itemIri']) {
            $iri = $result[0]['tincanItem_itemIri'];
        } else {
            $iri = $result[0]['tincanItem_itemLocation'];
        }

        // the activity title
        $activityTitle = false;
        if ($result[0]['activity_title']) {
            $activityTitle = $result[0]['activity_title'];
        }

        // the the items sub-activity's
        $activities = false;
        if ($result[0]['tincanItem_itemIri']) {
            $activities = $result[0]['tincanItem_itemActivities'];
        }

        // call private method
        $lrsCredentials = $this->getLrsCredentials( $optionService );

        /** @var \Tincan\RemoteLRS $lrs (initialise the LRS) */
        $lrs = new TinCan\RemoteLRS(
            $lrsCredentials['lrsUrl'],
            '1.0.1',
            $lrsCredentials['lrsUsername'],
            $lrsCredentials['lrsPassword']
        );

        $tcActivity = new TinCan\Activity(
            [ 'id' => $iri ]
        );

        // we are looking for a 'experienced' statements
        $verb = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/interacted' ]
        );
        $queryParams = [
            'verb' => $verb,
            'activity' => $tcActivity,
            'related_activities' => true
        ];

        if ($learner) {
            $actor = new TinCan\Agent();
            $learnerEmail = (isset($learner['email']) && strpos($learner['email'], "@") !== false) ? $learner['email'] : $learner['user_id'] . "@savv-e.com.au";
            $actor->setAccount(['name' => "" . $learnerEmail . "", 'homePage' => "". $iri . "" ]);
            $actor->setMbox($learnerEmail);
            $actor->setName($learner['name']);
            $queryParams['agent'] = $actor;
        }
        $statements =  $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams));
        $queryParams['verb'] = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/scored' ]
        );
        $statements = array_merge($statements, $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams)));

        if (count($statements)) {
            // send the IRI -> helpful filtering/sorting in the front-end
            $statements['iri'] = $iri;
            // send the sub-activity's to the front-end
            $statements['activities'] = $activities;
            // send the activity name/title
            $statements['activityTitle'] = $activityTitle;

            return $statements;
        }

        return false;

    }

    public function getInteractionsByActivityId($distributionId, $optionService, $activityId, $learner = false)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT distribution, trackerTincanItem, tincanItem
            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerTincanItem trackerTincanItem
                LEFT JOIN trackerTincanItem.item tincanItem
                WHERE distribution.distributionId = :distributionId";

        // distribution ID
        $params['distributionId'] = $distributionId;

        // execute query
        $dql = implode(' ', $dql);
        $result = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // get the homepage associated with the current activity
        if ($result[0]['tincanItem_itemHomepage']) {
            $homePage = $result[0]['tincanItem_itemHomepage'];
        } else {
            $homePage = $optionService->getHomepage();
        }

        $activities = false;
        // the the items sub-activity's
        if ($result[0]['tincanItem_itemIri']) {
            $activities = $result[0]['tincanItem_itemActivities'];
        }

        // call private method
        $lrsCredentials = $this->getLrsCredentials( $optionService );

        // intialise the LRS
        $lrs = new TinCan\RemoteLRS(
            $lrsCredentials['lrsUrl'],
            '1.0.1',
            $lrsCredentials['lrsUsername'],
            $lrsCredentials['lrsPassword']
        );

        $tcActivity = new TinCan\Activity(
            [ 'id' => $activityId ]
        );

        // we are looking for a 'experienced' statements
        $verb = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/interacted' ]
        );
        $queryParams = [
            'verb' => $verb,
            'activity' => $tcActivity,
            'related_activities' => true
        ];

        if ($learner) {
            $actor = new TinCan\Agent();
            $learnerEmail = (isset($learner['email']) && strpos($learner['email'], "@") !== false) ? $learner['email'] : $learner['user_id'] . "@savv-e.com.au";
            $actor->setAccount(['name' => "" . $learnerEmail . "", 'homePage' => "". $activityId . "" ]);
            $actor->setMbox($learnerEmail);
            $actor->setName($learner['name']);
            $queryParams['agent'] = $actor;
        }
        $statements =  $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams));
        $queryParams['verb'] = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/scored' ]
        );
        $statements = array_merge($statements, $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams)));

        if (count($statements)) {
            return $statements;
        }

        return false;

    }

    public function getExperiences($distributionId, $optionService, $learner = false)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT distribution, trackerTincanItem, tincanItem, activity
            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerTincanItem trackerTincanItem
                LEFT JOIN trackerTincanItem.item tincanItem
                LEFT JOIN distribution.activity activity
                WHERE distribution.distributionId = :distributionId";

        // distribution ID
        $params['distributionId'] = $distributionId;

        // execute query
        $dql = implode(' ', $dql);
        $result = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // get the homepage associated with the current activity
        if ($result[0]['tincanItem_itemHomepage']) {
            $homePage = $result[0]['tincanItem_itemHomepage'];
        } else {
            $homePage = $optionService->getHomepage();
        }

        // get the IRI of the current Activity
        if ($result[0]['tincanItem_itemIri']) {
            $iri = $result[0]['tincanItem_itemIri'];
        } else {
            $iri = $result[0]['tincanItem_itemLocation'];
        }

        // the activity title
        $activityTitle = false;
        if ($result[0]['activity_title']) {
            $activityTitle = $result[0]['activity_title'];
        }

        // the the items sub-activity's
        $activities = false;
        if ($result[0]['tincanItem_itemIri']) {
            $activities = $result[0]['tincanItem_itemActivities'];
        }

        // call private method
        $lrsCredentials = $this->getLrsCredentials( $optionService );

        // intialise the LRS
        $lrs = new TinCan\RemoteLRS(
            $lrsCredentials['lrsUrl'],
            '1.0.1',
            $lrsCredentials['lrsUsername'],
            $lrsCredentials['lrsPassword']
        );

        $tcActivity = new TinCan\Activity(
            [ 'id' => "". $iri ."" ]
        );

        // we are looking for a 'experienced' statements
        /*$verb = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/experienced' ]
        );*/
        $queryParams = [
            //'verb' => $verb,
            'activity' => $tcActivity,
            'related_activities' => true
        ];

        if ($learner) {
            $actor = new TinCan\Agent();
            $learnerEmail = (isset($learner['email']) && strpos($learner['email'], "@") !== false) ? $learner['email'] : $learner['user_id'] . "@savv-e.com.au";
            $actor->setAccount(['name' => "" . $learnerEmail . "", 'homePage' => "". $iri . "" ]);
            $actor->setMbox($learnerEmail);
            $actor->setName($learner['name']);
            $queryParams['agent'] = $actor;
        }
        $statements =  $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams));

        if (count($statements)) {
            // send the IRI -> helpful filtering/sorting in the front-end
            $statements['iri'] = $iri;
            // send the sub-activity's to the front-end
            $statements['activities'] = $activities;
            // send the activity name/title
            $statements['activityTitle'] = $activityTitle;

            return $statements;
        }

        return false;
    }

    public function getExperiencesByActivityId($distributionId, $optionService, $activityId, $learner = false)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT distribution, trackerTincanItem, tincanItem
            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.trackerTincanItem trackerTincanItem
                LEFT JOIN trackerTincanItem.item tincanItem
                WHERE distribution.distributionId = :distributionId";

        // distribution ID
        $params['distributionId'] = $distributionId;

        // execute query
        $dql = implode(' ', $dql);
        $result = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // get the homepage associated with the current activity
        if ($result[0]['tincanItem_itemHomepage']) {
            $homePage = $result[0]['tincanItem_itemHomepage'];
        } else {
            $homePage = $optionService->getHomepage();
        }

        $activities = false;
        // the the items sub-activity's
        if ($result[0]['tincanItem_itemIri']) {
            $activities = $result[0]['tincanItem_itemActivities'];
        }

        // call private method
        $lrsCredentials = $this->getLrsCredentials( $optionService );

        // intialise the LRS
        $lrs = new TinCan\RemoteLRS(
            $lrsCredentials['lrsUrl'],
            '1.0.1',
            $lrsCredentials['lrsUsername'],
            $lrsCredentials['lrsPassword']
        );

        $tcActivity = new TinCan\Activity(
            [ 'id' => "". $activityId ."" ]
        );

        // we are looking for a 'experienced' statements
        /*$verb = new TinCan\Verb(
            [ 'id' => 'http://adlnet.gov/expapi/verbs/experienced' ]
        );*/
        $queryParams = [
            //'verb' => $verb,
            'activity' => $tcActivity,
            'related_activities' => true
        ];

        if ($learner) {
            $actor = new TinCan\Agent();
            $learnerEmail = (isset($learner['email']) && strpos($learner['email'], "@") !== false) ? $learner['email'] : $learner['user_id'] . "@savv-e.com.au";
            $actor->setAccount(['name' => "" . $learnerEmail . "", 'homePage' => "". $activityId . "" ]);
            $actor->setMbox($learnerEmail);
            $actor->setName($learner['name']);
            $queryParams['agent'] = $actor;
        }
        $statements =  $this->__getMoreStatements($lrs, $lrs->queryStatements($queryParams));
        if (count($statements)) {
            return $statements;
        }

        return false;
    }

    /**
     * Fetch xAPI activities for the Admin report generator
     *
     * @param $siteId
     * @return array
     */
    public function getAllActivities($siteId)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $dql = [];
        $dql[] = "SELECT distribution, activity, tincanItems

            FROM Savvecentral\Entity\Distribution distribution
                LEFT JOIN distribution.activity activity
                LEFT JOIN distribution.learner learner
                LEFT JOIN learner.site site
                LEFT JOIN activity.tincanItems tincanItems
                WHERE site.siteId = :siteId
                AND activity.activityType IN (:activityType)
                AND activity.status IN (:activityStatus)
                AND distribution.status IN (:distributionStatus)";

        // site.site_id
        $params['siteId'] = $siteId;

        //learning_activity.status
        $params['activityStatus'] = ['active','inactive','new']; //@todo : Should inactive come up ?

        // activcity type (tincan only)
        $params['activityType'] = ['tincan'];

        // distribution
        $params['distributionStatus'] = ['active','new','expired','approved'];

        // group by
        $dql[] = "GROUP BY activity.activityId";

        // order by
        if (isset($orderBy)) {
            $orderBy = implode(", ", $orderBy);
            $dql[] = sprintf("ORDER BY %s", $orderBy);
        }

        // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        return $results;

    }

    public function generateReport($post, $optionService, $siteId, $filter = false)
    {
        /* @var $learnerService \Learner\Service\LearnerService */
        $learnerService  = $this->getServiceLocator()->get('Learner\Service');

        $entityManager   = $this->getEntityManager();
        $statementsArray = array();
        // determine if we are using a filter or post data
        if (isset($post['filter_id']) && !empty($post['filter_id']) && $filter['filter_id'] == $post['filter_id']) {

            // !! burn the strip using a filter !!
        //    $activityId = $filter['activity_id'];

            $activities = $filter['activity_iri'];

            $learners   = $filter['learner_id'];

            $status     = $filter['learner_status'];

            $groups     = $filter['group_id'];

            $completion = $filter['completion_status'];

        } else {

            // !! no filter present !!

            // get the activity IRI
            $iri = $post['activity_iri'];
            if ($iri == 'all') {
                $iri = $post['all_activities'];
            }
            $activities = explode(",", $iri);

            // get any sub activities
            $sub_iri = isset($post['sub_activity_iri']) ? $post['sub_activity_iri'] : false;
            if ($sub_iri == 'all') {
                $sub_iri = $post['all_sub_activities'];
            }
            $sub_activities = explode(",", $sub_iri);

            // get the learners
            $learners = isset($post['learner_id']) ? $post['learner_id'] : array();
            if (count($learners) == 0 || empty($learners)) {
                $learners = $post['all_learners'];
                $learners = explode(",", $learners);
            }

            // get the statuses for learners
            $status = isset($post['learner_status']) ? $post['learner_status'] : array();

            // get the groups
            $groups = isset($post['group_id']) ? $post['group_id'] : array();

            // get the completion statuses
            $completion = isset($post['completion_status']) ? $post['completion_status'] : array();
        }

        // these are the same with or without a filter being used
        if (count($status) == 0 || empty($status)) {
            $status = 'active';
        }
        if (count($groups) == 0 || empty($groups)) {
            $groups = false;
        }
        if (count($completion) == 0 || empty($completion)) {
            $completion = false;
        }

        // if $groups != false we need to re-fetch the learner array using groups service and the optional learner status
        if ($groups != false) {
            /* @var $groupService \Group\Service\GroupService */
            $groupService = $this->getServiceLocator()->get('Group\Service');
            $results = $groupService->findAllLearnersByGroupsAndStatus($groups, $status);
            $learners = array();
            foreach ($results as $result) {
                $learners[] = $result['user_id'];
            }
        }

        // if $status != 'active' and $groups == false and $completion == false we need to re-fetch the learner array using learner service
        if ($status != 'active' && $groups == false && $completion == false) {
            $results = $learnerService->findAllBySiteId($siteId, $status);
            $learners = array();
            foreach ($results as $result) {
                $learners[] = $result['user_id'];
            }
        }

        // iterate through the activities
        foreach ($activities as $activity) {

            $dql = [];
            $dql[] = "SELECT tincanItem, activity
            FROM Savvecentral\Entity\TincanItem tincanItem
                LEFT JOIN tincanItem.activity activity
                WHERE tincanItem.itemIri = :itemIri";

            // distribution ID
            $params['itemIri'] = $activity;

            // execute query
            $dql = implode(' ', $dql);
            $result = $entityManager->createQuery($dql)
                ->setParameters($params)
                ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
                ->getScalarResult();

            // get the homepage associated with the current activity
            if (isset($result[0]['tincanItem_itemHomepage']) && strlen($result[0]['tincanItem_itemHomepage'])) {
                $homePage = $result[0]['tincanItem_itemHomepage'];
            } else {
                $homePage = $optionService->getHomepage();
            }

            // call private method
            $lrsCredentials = $this->getLrsCredentials( $optionService );

            // intitalise the LRS
            $lrs = new TinCan\RemoteLRS(
                $lrsCredentials['lrsUrl'],
                '1.0.1',
                $lrsCredentials['lrsUsername'],
                $lrsCredentials['lrsPassword']
            );

            // this can be any IRI that represents either the base activity or a sub activity
            $tcActivity = new TinCan\Activity(
                [ 'id' => "". $activity . "" ]
            );

            // check to see if we only want learners with a specific tracking (completion) status for the current activity
            if ($completion != false) {
                /* @var $distributionService \Distribution\Learning\Service\LearningDistributionService */
                $distributionService = $this->getServiceLocator()->get('Distribution\Learning');
                $rows = $distributionService->findAllActiveLearnersByActivityIdStatus($result[0]['activity_activityId'], $completion, $status);
                $learners = array();
                foreach ($rows as $row) {
                    $learners[] = $row['userId'];
                }
            }

            // iterate through the learners and get statements for each learner that match the current activity
            foreach ($learners as $learner) {

                // get the learner details via service
                $learnerDetails = $learnerService->findOneByUserId($learner);

                // prepare the actor entity
                $actor = new TinCan\Agent();
                $learnerEmail = (isset($learnerDetails['email']) && strpos($learnerDetails['email'], "@") !== false) ? $learnerDetails['email'] : $learner . "@savv-e.com.au";
                $actor->setAccount(['name' => "" . $learnerEmail . "", 'homePage' => "". $activity . "" ]);
                $actor->setMbox($learnerEmail);
                $actor->setName($learnerDetails['name']);

                // get all statements filtered by actor only
                $response = $lrs->queryStatements([
                    'activity' => $tcActivity,
                    'agent' => $actor,
                    'related_activities' => true
                ]);

                // returned statements
                $statements = $this->__getMoreStatements($lrs, $response);

                // if this 'actor' has statement
                if (count($statements) >= 1) {
                    // if we have statements for this learner
                    $statementsArray[$learner]['name'] = $learnerDetails['name'];
                    $statementsArray[$learner]['email'] = $learnerDetails['email'];
                    $statementsArray[$learner][$activity]['title'] = $result[0]['activity_title'];
                    $statementsArray[$learner][$activity]['statements'] = $statements;
                }
            }
        }

        return $statementsArray;

    }

    /**
     * MyExperience report specifically generated for Inspire (SimHcp)
     *
     * ToDo: unlike the base generateReports() function this version requires that the $post['activity_iri'] to contains the Activity ID and not the Tincan Item's IRI value
     *
     * @param $post
     * @param $optionService
     * @param $siteId
     * @param bool $filter
     * @return array
     */
    public function generateInspireReport($post, $optionService, $siteId, $filter = false)
    {
        /* @var $learnerService \Learner\Service\LearnerService */
        $learnerService  = $this->getServiceLocator()->get('Learner\Service');

        $entityManager   = $this->getEntityManager();
        $statementsArray = array();

        // determine if we are using a filter or post data
        if (isset($post['filter_id']) && !empty($post['filter_id']) && $filter['filter_id'] == $post['filter_id']) {

            // !! burn the strip using a filter !!
            //    $activityId = $filter['activity_id'];

            $activities = $filter['activity_iri'];

            $learners   = $filter['learner_id'];

            $status     = $filter['learner_status'];

            $groups     = $filter['group_id'];

            $completion = $filter['completion_status'];

        } else {

            // !! no filter present !!

            // get the activity IRI
            $iri = $post['activity_iri'];
            if ($iri == 'all') {
                $iri = $post['all_activities'];
            }
            $activities = explode(",", $iri);

            // get the learners
            $learners = isset($post['learner_id']) ? $post['learner_id'] : array();
            if (count($learners) == 0 || empty($learners)) {
                $learners = $post['all_learners'];
                $learners = explode(",", $learners);
            }

            // get the statuses for learners
            $status     = null; //isset($post['learner_status']) ? $post['learner_status'] : array();

            // get the groups
            $groups     = null;

            // get the completion statuses
            /** Todo: !! monitor these values - it be be that 'incomplete' is not actually required here !!  */
            $completion = array('incomplete','completed'); //isset($post['completion_status']) ? $post['completion_status'] : array();
        }

        // these are the same with or without a filter being used
        if ( count($status) == 0 || empty($status) ) {
            $status = 'active';
        }
        if ( count($groups) == 0 || empty($groups) ) {
            $groups = false;
        }
        if ( count($completion) == 0 || empty($completion) ) {
            $completion = false;
        }

        // if $groups != false we need to re-fetch the learner array using groups service and the optional learner status
        if ($groups != false) {
            /* @var $groupService \Group\Service\GroupService */
            $groupService = $this->getServiceLocator()->get('Group\Service');
            $results = $groupService->findAllLearnersByGroupsAndStatus($groups, $status);
            $learners = array();
            foreach ($results as $result) {
                $learners[] = $result['user_id'];
            }
        }

        // if $status != 'active' and $groups == false and $completion == false we need to re-fetch the learner array using learner service
        if ($status != 'active' && $groups == false && $completion == false) {
            $results = $learnerService->findAllBySiteId($siteId, $status);
            $learners = array();
            foreach ($results as $result) {
                $learners[] = $result['user_id'];
            }
        }

        // iterate through the activities
        foreach ($activities as $activity) {

            // WHERE tincanItem.itemIri = :itemIri";
            $dql = [];
            $dql[] = "SELECT tincanItem, activity
            FROM Savvecentral\Entity\TincanItem tincanItem
                LEFT JOIN tincanItem.activity activity
                WHERE activity.activityId = :activityId";

            // distribution ID
            //$params['itemIri'] = $activity;
            $params['activityId'] = $activity;

            // execute query
            $dql = implode(' ', $dql);
            $result = $entityManager->createQuery($dql)
                ->setParameters($params)
                ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
                ->getScalarResult();

            // get the homepage associated with the current activity
            if (isset($result[0]['tincanItem_itemHomepage']) && strlen($result[0]['tincanItem_itemHomepage'])) {
                $homePage = $result[0]['tincanItem_itemHomepage'];
            } else {
                $homePage = $optionService->getHomepage();
            }

            // call private method
            $lrsCredentials = $this->getLrsCredentials( $optionService );

            // intitalise the LRS
            $lrs = new TinCan\RemoteLRS(
                $lrsCredentials['lrsUrl'],
                '1.0.1',
                $lrsCredentials['lrsUsername'],
                $lrsCredentials['lrsPassword']
            );

            // this can be any IRI that represents either the base activity or a sub activity
            $tcActivity = new TinCan\Activity(
                [ 'id' => "". $activity . "" ]
            );

            // check to see if we only want learners with a specific tracking (completion) status for the current activity
            if ($completion != false) {
                /* @var $distributionService \Distribution\Learning\Service\LearningDistributionService */
                $distributionService = $this->getServiceLocator()->get('Distribution\Learning');
                $rows = $distributionService->findAllActiveLearnersByActivityIdStatus($result[0]['activity_activityId'], $completion, $status);

                $learners = array();
                foreach ($rows as $row) {
                    $learners[] = $row['userId'];
                }
            }

            // iterate through the learners and get statements for each learner that match the current activity
            foreach ($learners as $learner) {

                // get the learner details via service
                $learnerDetails = $learnerService->findOneByUserId($learner);

                // prepare the actor entity
                $actor = new TinCan\Agent();
                //    $actor->setAccount(['name' => "" . $learner . "", 'homePage' => "". $homePage . "" ]);
                $learnerEmail = (isset($learnerDetails['email']) && strpos($learnerDetails['email'], "@") !== false) ? $learnerDetails['email'] : $learner . "@savv-e.com.au";

                $actor->setMbox($learnerEmail);
                $actor->setName($learnerDetails['name']);

                // get all statements filtered by actor only
                $response = $lrs->queryStatements([
                    'agent' => $actor
                ]);

                // returned statements
                $statements = $response->content->getStatements();

                // if this 'actor' has statement
                if (count($statements) >= 1) {

                    $arr = array();
                    // look for statements that match or contain the current IRI
                    foreach ($statements as $statement) {
                        if ($statement->getTarget()->getId() == $result[0]['tincanItem_itemIri'] || strpos($statement->getTarget()->getId(), $result[0]['tincanItem_itemIri']) !== false) {
                            $arr[] = $statement;
                        }
                    }
                    if (count($arr) >= 1) {
                        // if we have statements for this learner
                        $statementsArray[$learner]['name'] = $learnerDetails['name'];
                        $statementsArray[$learner]['email'] = $learnerDetails['email'];
                        $statementsArray[$learner][$result[0]['tincanItem_itemIri']]['title'] = $result[0]['activity_title'];
                        $statementsArray[$learner][$result[0]['tincanItem_itemIri']]['statements'] = $arr;
                    }
                }
            }
        }
        return $statementsArray;
    }

    /**
     * Pad a string (integer) with leading zeros
     *
     * @param $input
     * @param $num
     * @return string
     */
    private function padZero($input, $num) {
        for ($i = 0; $i < $num; $i++) {
            if (strlen($input) == $num) { break; }
            $input = '0'.$input;
        }
        return $input;
    }

    /**
     * Convert a PT time to a visual hours:minutes:seconds output (0000:00:00)
     *
     * @param $input
     * @return string
     */
    private function extractTimeFromPTTime($input) {
        $str = str_replace("PT", "", $input);
        $arr = explode("M", $str);
        $minutes = $arr[0];
        $seconds = str_replace("S", "", $arr[1]);

        $hours = '0';
        if ($minutes >= 60) {
            $hours   = floor($minutes / 60);
            $minutes = $minutes - ($hours * 60);
        }
        $output = $this->padZero($hours, 4) . ':' . $this->padZero($minutes, 2) . ':' . $this->padZero($seconds, 2);
        return $output;
    }

    public function convertReportToArray($results)
    {
        if ($results) {
            $output= array();

            foreach ($results as $uid => $result) {

                // the first level contains the Learner details
                $learnerName = $result['name'];
                $learnerEmail = $result['email'];

                // clear the Learner name, enail and iterate the $result array
                unset($result['name']);
                unset($result['email']);

                foreach ($result as $iri => $row) {

                    // second level contained the Activity details
                    $activityTitle = $row['title'];

                    // now we iterate the statements and build the 'row' data array
                    foreach ($row['statements'] as $statement) {

                        // create an empty array for each iteration
                        $arr = array();

                        // learner
                        $arr['learner_id']    = $uid;
                        $arr['learner_name']  = $learnerName;
                        $arr['learner_email'] = $learnerEmail;

                        // activity
                        $arr['activity_iri']   = $iri; // the base activity IRI
                        $arr['activity_title'] = $activityTitle;

                        // statement data
                        // the IRI for the sub-activity
                        $targetId   = $statement->getTarget()->getId();

                        // the IRI for any group context for this statement
                        $groupingId = $statement->getContext()->getContextActivities()->getGrouping()[0]->getId();
                        $arr['group_iri'] = isset($groupingId) && $groupingId != null ? $groupingId : '';

                        // verb
                        $verb = $statement->getVerb()->getDisplay()->asVersion('en-US');
                        $arr['activity_verb'] = Stdlib\StringUtils::capitalise($verb['en-US']);

                        // activity definition name from target -> definition -> name
                        $page = $statement->getTarget()->getDefinition()->getName()->asVersion('en-US');
                        $arr['activity_definition_name'] = $page['en-US'];

                        // activity description from target -> definition -> description
                        $desc = $statement->getTarget()->getDefinition()->getDescription()->asVersion('en-US');
                        $arr['activity_definition_description'] = $desc['en-US'];

                        // response grouping from result -> extensions (if present, the group the response belongs to)
                        $group = null; if ($statement->getResult()) { $group = $statement->getResult()->getExtensions()->asVersion($groupingId); }
                        // this uses the groupingID from context -> contactActivities -> grouping
                        $arr['response_group'] = isset($group[$groupingId]) ? $group[$groupingId] : '';

                        // learner response for any interaction from result -> response
                        $response = null; if ($statement->getResult()) { $response = $statement->getResult()->getResponse(); }
                        $arr['response'] = isset($response) ? $response : '';

                        // the duration value from result -> duration (returns as PT time (minutes and seconds)
                        $duration = null; if ($statement->getResult()) { $duration = $statement->getResult()->getDuration(); }
                        $arr['duration'] = isset($duration) ? $this->extractTimeFromPTTime($duration) : '';

                        // timestamp
                        $timestamp = $statement->getTimestamp();
                        $arr['timestamp'] = isset($timestamp) ? date("d-m-Y h:i:s", strtotime($timestamp)) : '';

                        // save the array into the output array
                        $output[] = $arr;

                    }
                }
            }
            return $output;
        }
        return false;
    }

    private function __getMoreStatements($lrs, $response)
    {
        $statements = array();
        if (!empty($response)) {
            if (isset($response->content) && !is_string($response->content)) {
                $statements = $response->content->getStatements();
            }
            while(!empty($response->content) && !is_string($response->content) && !empty($response->content->getMore())) {
                $response = $lrs->moreStatements($response->content->getMore());
                if (isset($response->content) && !is_string($response->content)) {
                    $statements = array_merge($statements, $response->content->getStatements());
                }
            }
        }
        return $statements;
    } 
}
