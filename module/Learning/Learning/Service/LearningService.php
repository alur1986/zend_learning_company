<?php

namespace Learning\Service;

use Learning\EventManager\Event;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;

class LearningService extends AbstractService
{

    /**
     * Find ONE learning activity by activity ID
     *
     * @param integer $activityId
     * @return Entity\LearningActivity
     */
    public function findOneLearningActivityById ($activityId)
    {
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.site', 'site')
            ->select('activity, site')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        return $repository->fetchOne($qb);
    }

    /**
     * Find ONE Written Assessment by activity ID
     *
     * @param integer $activityId
     * @return Entity\LearningActivity
     */
    public function findOneWrittenAssessmentyById ($activityId)
    {
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.site', 'site')
            ->leftJoin('activity.assessment', 'assessment')
            ->leftJoin('assessment.questions', 'questions')
            ->select('activity, site, assessment, questions')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        return $repository->fetchOne($qb);
    }

    /**
     * Find ONE On The Job by activity ID - identical to the one above
     *
     * @param integer $activityId
     * @return Entity\LearningActivity
     */
    public function findOneOTJById ($activityId)
    {
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.site', 'site')
            ->leftJoin('activity.assessment', 'assessment')
            ->leftJoin('assessment.questions', 'questions')
            ->select('activity, site, assessment, questions')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
       return $repository->fetchOne($qb);
    }

    /**
     * Find ALL learning activities by site ID
     *
     * @param integer $siteId Current site ID
     * @return ArrayCollection
     */
    public function findAllLearningActivitiesOrderByTitle ($siteId)
    {
        $repository = $this->learningRepository();

        $dql = "SELECT activity, assessment
                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.assessment assessment
                WHERE site.siteId = :siteId
                AND activity.status NOT IN ('deleted')
                GROUP BY activity.activityId
                ORDER BY activity.title ASC";

        $params['siteId'] = $siteId;

        // execute query
        return $repository->fetchCollection($dql, $params);
    }

    /**
     * Find ALL learning activities by site ID
     *
     * @param integer $siteId Current site ID
     * @param string $groupType Learning Activity group type (event or learning or assessment or scorm)
     * @return ArrayCollection
     */
    public function findAllLearningActivitiesBySiteId ($siteId, $groupType = null)
    {
        $repository = $this->learningRepository();

        switch ($groupType) {
            case 'event':
                $activityType = [
                    'face-to-face',
                    'webinar'
                ];
                break;

            case 'learning':
                $activityType = [
                    'scorm12',
                    'resources',
                    'resource',
                    'tincan'
                ];
                break;

            case 'assessment':
                $activityType = [
                    'written-assessment',
                    'on-the-job-assessment'
                ];
                break;

            case 'scorm':
                $activityType = [
                    'scorm12',
                    'tincan'
                ];
                break;

            default:
                $activityType = [];
                break;
        }

        // create query
        /*$dql[] = "SELECT activity, site, scorm12Activity, assessment
                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.scorm12Activity scorm12Activity
                LEFT JOIN activity.assessment assessment
                WHERE site.siteId = :siteId
                AND activity.status NOT IN ('deleted')";*/

        $dql[] = "SELECT activity, assessment
                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.assessment assessment
                WHERE site.siteId = :siteId
                AND activity.status NOT IN ('deleted')";

        $params['siteId'] = $siteId;

        if ($activityType) {
            $dql[] = "AND activity.activityType IN (:activityType)";
            $params['activityType'] = $activityType;
        }

        $dql[] = "GROUP BY activity.activityId";
        $dql[] = "ORDER BY activity.activityId DESC";
   //     $dql[] = "ORDER BY activity.title ASC";

        // execute query
        return $repository->fetchCollection($dql, $params);
    }

    /**
     * Find ALL learning activities by site ID
     *
     * @param integer $siteId Current site ID
     * @return ArrayCollection
     */
    public function findAllLicensedActivitiesBySiteId ($siteId)
    {
        $repository = $this->learningRepository();

        // only scorm activities
        $activityType = [
            'scorm12'
        ];

        // create query
        $dql[] = "SELECT activity, site, scorm12Activity, assessment
                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.scorm12Activity scorm12Activity
                LEFT JOIN activity.assessment assessment
                WHERE activity.status NOT IN ('deleted')
                AND activity.licensed > 0";

        // $params['siteId'] = $siteId; //       WHERE site.siteId = :siteId

        if ($activityType) {
            $dql[] = "AND activity.activityType IN (:activityType)";
            $params['activityType'] = $activityType;
        }

        $dql[] = "GROUP BY site.siteId";

    //    $dql[] = "GROUP BY activity.activityId";
        $dql[] = "ORDER BY activity.title ASC";
        //     $dql[] = "ORDER BY activity.title DESC";


        // execute query
        return $repository->fetchCollection($dql, $params);
    }

    /**
     * Find ALL learning activities by activity type
     *
     * @param string|array $activityType
     * @return ArrayCollection
     */
    public function findAllLearningActivitiesByType ($activityType, $siteId)
    {
        $activityType = (array) $activityType;
        $activityStatus = [ 'new', 'active', 'inactive' ];
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.site', 'site')
            ->select('activity, site')
            ->where('activity.activityType IN (:activityType) AND site.siteId = :siteId AND activity.status IN (:activityStatus)')
            ->setParameter('activityType', $activityType)
            ->setParameter('siteId', $siteId)
            ->setParameter('activityStatus', $activityStatus)
            ->add('orderBy', 'activity.title ASC');

        // execute query
        return $repository->fetchCollection($qb);
    }

    /**
     * * Create ONE learning activity in repository
     *
     * @param $data
     * @return Stdlib\stdClass|Entity\LearningActivity
     * @throws \Exception
     */
    public function createActivity ($data)
    {
        try {
	        $data = Stdlib\ObjectUtils::extract($data);
	        $entityManager = $this->getEntityManager();

	        // define the site ID
	        $routeMatch = $this->routeMatch();
	        $siteId = isset($data['site']) ? $data['site_id'] : $routeMatch->getParam('site_id');

	        // get the site entity instance
	        $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
	        $data['site'] = $site;

            // if we need a plan
            $planIds = false;
            if (isset($data['planId']) && is_array($data['planId'])) {
                $planIds = $data['planId'];
                // this needs to be cleared as it wont propagate
                unset($data['planId']);
            }

	        // create new entity
	        $activity = new Entity\LearningActivity();
	        $activity = Stdlib\ObjectUtils::hydrate($data, $activity);

	        // set the category association
			$activity['category'] = isset($data['category_id']) && $data['category_id'] ? $entityManager->getReference('Savvecentral\Entity\Taxonomy', $data['category_id']) : null;

	        // save in repository
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

            // save any learning plan reference
            if (isset($planIds)) {
                foreach ($planIds as $planId) {
                    $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
                    if (isset($plan)) {

                        // clear any existing references for this Learning Plan + Activity combination !! not required for create !!
                        /* $entityManager->createQuery('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.plans = :planId AND learningPlanActivity.activities = :activityId')
                            ->setParameter('planId', $planId)
                            ->setParameter('activityId', $activityId)
                            ->execute(); */

                        // create new entity
                        $lpa = new Entity\LearningPlanActivity();
                        $arr = array('plan_id' => $planId, 'activity_id' => $activity);

                        // set the Plan and Activity entities
                        $arr['plans'] = $plan;
                        $arr['activities'] = $activity;
                        $lpa = Stdlib\ObjectUtils::hydrate($arr, $lpa);

                        // save in repository
                        $entityManager->persist($lpa);
                        $entityManager->flush($lpa);
                    }
                }
            }
	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE learning activity in repository
     *
     * @param array|\Traversable $data
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function updateActivity ($data)
    {
        try {
            $activityId = $data['activity_id'];
	        $data = Stdlib\ObjectUtils::extract($data);

	        // check if activity ID was provided
	        if (!(isset($data['activity_id']) && $data['activity_id'])) {
	            throw new Exception\InvalidArgumentException(sprintf('Cannot update activity. Activity ID was not provided'), 404, null);
	        }

            // titty manager
	        $entityManager = $this->getEntityManager();

            // if we need a plan
            $planIds = false;
            if (isset($data['plan_id']) && $data['plan_id'][0] != '') {
                $planIds = $data['plan_id'];
                // this should to be cleared as it wont propagate
                unset($data['plan_id']);
            }

            /* @var $activity Entity\LearningActivity */
	        $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);
	        $activity = Stdlib\ObjectUtils::hydrate($data, $activity);

	        // set the category association
			$activity['category'] = isset($data['category_id']) && $data['category_id'] ? $entityManager->getReference('Savvecentral\Entity\Taxonomy', $data['category_id']) : null;

            // forcibly remove the hasPlans if no plans are set
            if (empty($planIds) || (is_array($planIds) && count($planIds) == 0)) {
                $activity->setHasPlans(null);
            }

	        // save in repository
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

            // save any learning plan reference
            if (isset($planIds) && is_array($planIds)) {
                foreach ($planIds as $planId) {
                    $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
                    if (isset($plan)) {

                        // clear any existing references for this Learning Plan + Activity combination
                            $entityManager->createQuery('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.plans = :planId AND learningPlanActivity.activities = :activityId')
                                ->setParameter('planId', $planId)
                                ->setParameter('activityId', $activityId)
                                ->execute();

                        // create new entity
                        $lpa = new Entity\LearningPlanActivity();
                        $arr = array('plan_id' => $planId, 'activity_id' => $activity);

                        // set the Plan and Activity entities
                        $arr['plans'] = $plan;
                        $arr['activities'] = $activity;
                        $lpa = Stdlib\ObjectUtils::hydrate($arr, $lpa);

                        // save in repository
                        $entityManager->persist($lpa);
                        $entityManager->flush($lpa);
                    }
                }

            } else {
                // in case this activity is being remove from a playlist
                $this->removeActivityFromPlans( $activityId );
            }

	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE learning activity from repository
     *
     * @param integer $activityId
     * @throws \Exception
     */
    public function deleteActivity ($activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // retrieve the learning activity
	        $repository = $this->learningRepository();
	        $activity = $repository->findOneByActivityId($activityId);

	        // save in repository
	        $activity['status'] = 'deleted';
	        $activityType       = $activity['activity_type'];
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

	        // only run if 'activity_type' is in this array
	        $types = array('written-assessment','face-to-face','on-the-job-assessment','webinar');
	        if (in_array($activityType, $types)) {
	            $repository = $this->eventRepository();
    	        // get all related 'events'
    	        // Savvecentral\Entity\Event
    	        $dql[] = "SELECT events
                    FROM Savvecentral\Entity\Event events
                    WHERE events.activity = :activityId
                    AND events.status NOT IN ('deleted')";
    	        $params['activityId'] = $activityId;

    	        $results = $repository->fetchCollection($dql, $params);

    	        foreach ($results as $result) {
    	            // set each events status to 'deleted' too
    	            $eventId = $result['event_id'];
    	            $event = $repository->findOneByEventId($eventId);

    	            // update status in repository
    	            $event['status'] = 'deleted';
    	            $entityManager->persist($event);
    	            $entityManager->flush($event);
    	        }
	        }

	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Activate ONE learning activity from repository
     *
     * @param integer $activityId
     * @throws \Exception
     */
    public function activateActivity ($activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // retrieve the learning activity
	        $repository = $this->learningRepository();
	        $activity = $repository->findOneByActivityId($activityId);

	        // save in repository
	        $activity['status'] = 'active';
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Deactivate ONE learning activity from repository
     *
     * @param integer $activityId
     * @throws \Exception
     */
    public function deactivateActivity ($activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // retrieve the learning activity
	        $repository = $this->learningRepository();
	        $activity = $repository->findOneByActivityId($activityId);

	        // save in repository
	        $activity['status'] = 'inactive';
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Duplicate ONE learning activity
     *
     * @param integer $activityId
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function duplicateActivity ($activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // retrieve the learning activity
	        $repository = $this->learningRepository();
	        $original = $repository->findOneByActivityId($activityId);

	        // extract data from the original facilitator entity
	        $data = Stdlib\ObjectUtils::extract($original);
	        unset($data['activity_id']);
            unset($data['tincan_items']);

	        // create new entity
            /* @var $activity Entity\LearningActivity */
	        $activity = new Entity\LearningActivity();
	        $activity = Stdlib\ObjectUtils::hydrate($data, $activity);
	        $activity['date_created'] = new \DateTime();
	        $activity['date_updated'] = null;

            // update the activity name so it can be recognised as a copy
            $title = $activity['title'] . " Duplicated-" . time();
            $activity['title'] = $title;

            if ($activity['activity_type'] == 'tincan')  {
                // get the activity
                $tincanActivityData = Stdlib\ObjectUtils::extract($activity['tincan_activity']);
                $tincanActivity = new Entity\TincanActivity();
                $tincanActivity = Stdlib\ObjectUtils::hydrate($tincanActivityData, $tincanActivity);

                // get the item
                $tincanItemRepository = $this->tincanItemRepository();

                // create query
                $qb = $tincanItemRepository->createQueryBuilder('tincanItem')
                    ->select('tincanItem')
                    ->where('tincanItem.activity = :activityId')
                    ->setParameter('activityId', $activityId);

                // execute query
                $tinncanItem = $tincanItemRepository->fetchOne($qb);
                $tincanItemData =  Stdlib\ObjectUtils::extract($tinncanItem);
                unset($tincanItemData['item_id']);
                // create new item
                $tincanItem = new  Entity\TincanItem();
                $tincanItem = Stdlib\ObjectUtils::hydrate($tincanItemData, $tincanItem);

            }

            // remove any hasPlans relationships
            $activity->setHasPlans(null);

	        // save activity in repository
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

            if ($activity['activity_type'] == 'tincan') {
                $tincanActivity['activity'] = $activity;
                $tincanItem['activity']     = $activity;

                // save tincan activity in repository
                $entityManager->persist($tincanActivity);
                $entityManager->flush($tincanActivity);

                // save the tincan item in repository
                $entityManager->persist($tincanItem);
                $entityManager->flush($tincanItem);
            }

	        // trigger event listeners
	        $this->triggerListeners(new Event(Event::EVENT_DUPLICATE, $this, [ 'original' => $original, 'duplicate' => $activity ]));

	        return $activity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get All Learning Plan details
     *
     * @param integer $activityId
     * @return Entity\LearningPlan
     * @throws \Exception
     */
    public function getAllLearningPlansBySiteId ($siteId, $context = false)
    {
        try {

            // retrieve all learning plans for a site
            $repository = $this->planRepository();

            // set the $status according to context
            if ($context == 'admin') {
                $status = ['deleted'];
            } else {
                $status = ['inactive','deleted'];
            }

            $dql[] = "SELECT plans
                    FROM Savvecentral\Entity\LearningPlan plans
                    WHERE plans.site = :siteId
                    AND plans.status NOT IN (:status)";

            $params['siteId'] = $siteId;
            $params['status'] = $status;

            return $repository->fetchCollection($dql, $params);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Find ALL 'active' learning activities by activity ID's
     *
     * @param integer $planId (Current site ID)
     * @return ArrayCollection
     */
    public function findAllLearningActivitiesByPlanId ($planId)
    {
        $repository = $this->learningRepository();


        // create query
        $dql[] = "SELECT activity
                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.hasPlans plans
                WHERE plans.plans = :planId AND activity.status = 'active'";

        $params['planId'] = $planId;

        // execute query
        return $repository->fetchCollection($dql, $params);
    }

    /**
     * Find ALL learning plans by activity ID's
     *
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function findAllLearningPlansByActivityId ($activityId)
    {
        $repository = $this->planActivityRepository();

        // create query
        $dql[] = "SELECT hasPlans
                FROM Savvecentral\Entity\LearningPlanActivity hasPlans
                WHERE hasPlans.activities = :activityId";

        $params['activityId'] = $activityId;

        // execute query
        return $repository->fetchCollection($dql, $params);
    }

    public function removeActivityFromPlans( $activityId )
    {
        $hasPlans = $this->findAllLearningPlansByActivityId( $activityId );
        if ($hasPlans && count($hasPlans)) {

            // titty manager
            $entityManager = $this->getEntityManager();

            foreach ($hasPlans as $plan) {
                $id = $plan['id'];
                $entityManager->createQuery('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.id = :id AND learningPlanActivity.activities = :activityId')
                    ->setParameter('id', $id)
                    ->setParameter('activityId', $activityId)
                    ->execute();
            }
        }
    }

    /**
     * Get all activities set for Auto Distribute on Registration
     *
     * @param integer $siteId
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function getAutoDistributeOnRegister($siteId) {
        try {
            // retrieve repo thingy for learningActivity
            $repository = $this->learningRepository();
            $status = ['deleted','inactive'];

            $dql[] = "SELECT activitys, plans
                    FROM Savvecentral\Entity\LearningActivity activitys
                    LEFT JOIN activitys.hasPlans plans
                    WHERE activitys.site = :siteId
                    AND activitys.status NOT IN (:status)
                    AND activitys.autoDistribute = 1
                    AND activitys.autoDistributeOnRegistration = 1
                    ORDER BY plans.plans, activitys.ordering";

            $params['siteId'] = $siteId;
            $params['status'] = $status;

            return $repository->fetchCollection($dql, $params);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all 'Course activities' set for Auto Distribute on Registration
     *
     * @param integer $courseId
     * @param integer $siteId
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function getAutoDistributeCourseOnRegister($courseId, $siteId) {
        try {
            // retrieve repo thingy for learningActivity
            $repository = $this->learningRepository();
            $status = ['deleted','inactive'];

            $dql[] = "SELECT activitys, plans
                    FROM Savvecentral\Entity\LearningActivity activitys
                    LEFT JOIN activitys.hasPlans plans
                    WHERE activitys.site = :siteId
                    AND plans.plans = :courseId
                    AND activitys.status NOT IN (:status)
                    AND activitys.autoDistribute = 1
                    AND activitys.autoDistributeOnRegistration = 1
                    ORDER BY plans.plans, activitys.ordering";

            $params['siteId']   = $siteId;
            $params['courseId'] = $courseId;
            $params['status']   = $status;

            return $repository->fetchCollection($dql, $params);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all activities set for Auto Distribute on Login
     *
     * @param integer $siteId
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function getAutoDistributeOnLogin($siteId) {
        try {
            // retrieve repo thingy for learningActivity
            $repository = $this->learningRepository();
            $status = ['deleted','inactive'];

            $dql[] = "SELECT activitys, plans
                    FROM Savvecentral\Entity\LearningActivity activitys
                    LEFT JOIN activitys.hasPlans plans
                    WHERE activitys.site = :siteId
                    AND activitys.status NOT IN (:status)
                    AND activitys.autoDistribute = 1
                    AND activitys.autoDistributeOnLogin = 1
                    ORDER BY plans.plans, activitys.ordering";

            $params['siteId'] = $siteId;
            $params['status'] = $status;

            return $repository->fetchCollection($dql, $params);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function runMigrationCode($cdnPath,$cdnFilePath)
    {
        // get the full hostname;
        $hostname = $_SERVER['SERVER_NAME'];
        $arr      = explode(".", $hostname);
        $host     = array_shift($arr);
        $domain = implode(".", $arr);

        // get the sites first
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Site');

        $status = array('new','active','inactive');
        $dql[] = "SELECT sites
            FROM Savvecentral\Entity\Site sites
            WHERE sites.status IN (:status)
            ORDER BY sites.siteId";

        $params['status'] = $status;

        $sites = $repository->fetchCollection($dql, $params);

        // activity suppository
        $activityRepository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');

        // item suppository
        $itemRepository = $entityManager->getRepository('Savvecentral\Entity\Scorm12Item');

        foreach ($sites as $site) {

            $siteId = $site['siteId'];
            $url = $site['url'];
            echo '<br/><br/>siteId: ' . $siteId . ', url: ' . $url . ' <br/>';

            // get this site 'activities'
            $type = 'scorm12';
            $dql = array(); $params = array();
            $dql[] = "SELECT activities
                FROM Savvecentral\Entity\LearningActivity activities
                WHERE activities.site = :siteId AND activities.activityType = :type
                ORDER BY activities.activityId";

            $params['siteId'] = $siteId;
            $params['type']   = $type;

            $activities = $activityRepository->fetchCollection($dql, $params);

            if (isset($activities) && count($activities)) {
                
                foreach ($activities as $activity) {

                    $activityId = $activity['activityId'];
                    echo 'activity ID: ' . $activityId . '<br/>';

                    // get the scorm item for launch file and item ID
                    $status = 'active';
                    $dql = array(); $params = array();
                    $dql[] = "SELECT item
                        FROM Savvecentral\Entity\Scorm12Item item
                        WHERE item.activity = :activityId AND item.status = :status";

                    $params['activityId'] = $activityId;
                    $params['status']   = $status;

                    $item = $itemRepository->fetchOne($dql, $params);

                    $location = isset($item['itemLocation']) ? $item['itemLocation'] : false;

                    $filePath = $cdnFilePath . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . $activityId . DIRECTORY_SEPARATOR . 'course';

                    if ($location && file_exists($filePath)) {

    //                    echo 'file Path: ' . $filePath . '<br />';

                        // if this is an 'Elucidat' Scorm package we need to add a few lines of code to the beginning of its JS file
                        $elucidatJs = $filePath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'elucidat-v2.0.min.js';
                //        echo 'elucidat: ' . $elucidatJs . '<br />';

                        // to test for an AEC style course
                        $aecBazinga = $filePath . DIRECTORY_SEPARATOR . 'bazinga-framework' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bazinga.vendor.min.js';
                 //       echo 'bazinga: ' . $aecBazinga . '<br />';

                        // for custom HTML
                        $customScorm = $filePath . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'scorm' . DIRECTORY_SEPARATOR . 'scorm.js';
                //        echo 'custom: ' . $customScorm . '<br />';

                        // for custom HTML type 2
                        $customScorm2 = $filePath . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'scorm' . DIRECTORY_SEPARATOR . 'scorm.js';
                //        echo 'custom 2: ' . $customScorm2 . '<br />';

                        // lectora (Trivantis)
                        $lectora = $filePath . DIRECTORY_SEPARATOR . 'trivantis.js';

                //        if (strpos($location, "/") !== false) {
                //            $filelocation = substr($location, strripos($location, "/")+1, strlen($location));
                //        }
                //        echo 'location: ' . $filelocation . '<br />';

                        if (file_exists( $elucidatJs )) {

    //                        echo 'has elucudat...<br/>';
                            // get the file contents
                            $contents = file_get_contents($elucidatJs);
                            $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;" . $contents;

                  //          file_put_contents($elucidatJs, $contents);

                        } elseif (file_exists( $aecBazinga )) {

    //                        echo 'has bazinga...<br/>';
                            // get the file contents
                            $contents = file_get_contents($aecBazinga);
                            $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                  //          file_put_contents($aecBazinga, $contents);

                            // need this for the close-down page
                            $file =  $filePath . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'complete.html';
                            if (file_exists( $file )) {

                                // get the file contents
                                $contents = file_get_contents($file);
                                $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                  //              file_put_contents($file, $contents);

                            }

                        } elseif (file_exists( $customScorm )) {

     //                       echo 'has custom scrom...<br/>';
                            // Custom Scorm12 module - add JS to the scrom.js file
                            // get the file contents
                            $contents = file_get_contents($customScorm);
                            $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                //            file_put_contents($customScorm, $contents);

                        } elseif (file_exists( $customScorm2 )) {

                //            echo 'has custom type 2 scrom...<br/>';
                            // Custom Scorm12 module - add JS to the scorm.js file
                            // get the file contents
                            $contents = file_get_contents($customScorm2);
                            $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                //            file_put_contents($customScorm2, $contents);

                        } elseif (file_exists( $lectora )) {

                            echo 'has lectora ..trivantis.. file..<br/>';
                            // Custom Scorm12 module - add JS to the scrom.js filer
                            // get the file contents
                            $contents = file_get_contents($lectora);
                            $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                            file_put_contents($lectora, $contents);

                        } else {
                            // for modules with launch file within a directory below the course top-level
                            if (strpos($location, "course/Lesson") !== false) {
                                $location = substr($location, strrpos($location, "/") + 1, strlen($location)); // gets a 'launch fle' relative the top-level of the course
                                $launchFile = $filePath . DIRECTORY_SEPARATOR . 'Lesson_1' . DIRECTORY_SEPARATOR . $location;
                                if (file_exists($launchFile)) {
                                    echo 'has second level launch file..... ' . $launchFile . '<br/>';
                                    $contents = file_get_contents($launchFile);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($launchFile, $contents);
                                }
                            }

                                //    echo 'the big ELSE has been detected...<br/>';
                            // this will add the CORS JS code into all other modules into the 'launch' file - just before the closing </body> tag

                        //    $location = substr($location, strrpos($location, "/")+1, strlen($location));
                        /*    $launchFile = $filePath . DIRECTORY_SEPARATOR . $location;
                            echo 'launch file being sought:' . $launchFile . '<br/>';
                            if (file_exists($launchFile)) {

                                echo 'modifying launch file...<br/>';
                                $contents = file_get_contents($launchFile);
                                $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                file_put_contents($launchFile, $contents);
                            }
                            // for Articulate -> lms/blank.html && lms/AICCComm.html && lms/lms.js
                            $file = $filePath . DIRECTORY_SEPARATOR .'lms/blank.html';
                            if (file_exists($file)) {

                                echo 'modifying blank.html...<br/>';
                                $contents = file_get_contents($file);
                                $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                file_put_contents($file, $contents);
                            }
                            $file = $filePath . DIRECTORY_SEPARATOR .'lms/AICCComm.html';
                            if (file_exists($file)) {

                                echo 'modifying AICCComm.html...<br/>';
                                $contents = file_get_contents($file);
                                $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                file_put_contents($file, $contents);
                            }
                            $file = $filePath . DIRECTORY_SEPARATOR .'lms/lms.js';
                            if (file_exists($file)) {

                                echo 'modifying lms.js...<br/>';
                                $contents = file_get_contents($file);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */ /*
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;" . $contents;

                                file_put_contents($file, $contents);
                            }
                            // for some HTML courses -> end_popup.html
                            $file = $filePath . DIRECTORY_SEPARATOR .'end_popup.html';
                            if (file_exists($file)) {

                                echo 'modifying end_popup.html...<br/>';
                                $contents = file_get_contents($file);
                                $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                file_put_contents($file, $contents);
                            }
 */
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the learning activity doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learningRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\LearningActivity');
    }

    /**
     * Get the tincan item doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function tincanItemRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\TincanItem');
    }


    /**
     * Get the notification doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function eventRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Event');
        return $repository;
    }

    /**
     * Get the learning plan doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function planRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\LearningPlan');
    }
    /**
     * Get the learning plan doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function planActivityRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\LearningPlanActivity');
    }
}
