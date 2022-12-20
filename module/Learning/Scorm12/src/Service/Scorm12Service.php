<?php

namespace Scorm12\Service;

use Learning\Service\LearningService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\ArrayCollection;

class Scorm12Service extends AbstractService
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
            ->leftJoin('activity.scorm12Items', 'items')
            ->leftJoin('activity.scorm12Activity', 'scorm12Activity')
            ->leftJoin('activity.hasPlans', 'plan')
            ->select('activity, site, items, scorm12Activity, plan')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE Scorm12 course item from the repository
     *
     * @param integer $itemId
     * @return Entity\Scorm12Item
     */
    public function findOneItemById ($itemId)
    {
        $repository = $this->itemsRepository();

        // create query
        $qb = $repository->createQueryBuilder('item')
            ->leftJoin('item.activity', 'activity')
            ->select('item, activity')
            ->where('item.itemId = :itemId')
            ->setParameter('itemId', $itemId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ALL items by identifiers
     *
     * @param array $identifiers
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function findAllItemsByIdentifier (array $identifiers, $activityId)
    {
        $repository = $this->itemsRepository();

        // create query
        $qb = $repository->createQueryBuilder('item')
            ->leftJoin('item.activity', 'activity')
            ->select('item, activity')
            ->where('item.identifier IN (:identifier) AND activity.activityId = :activityId')
            ->setParameter('identifier', $identifiers)
            ->setParameter('activityId', $activityId);

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ONE item in an activity using its identifier
     *
     * @param string $identifier
     * @param integer $activityId
     * @return Entity\Scorm12Item
     */
    public function findOneItemByIdentifier ($identifier, $activityId)
    {
        $repository = $this->itemsRepository();

        // create query
        $qb = $repository->createQueryBuilder('item')
            ->leftJoin('item.activity', 'activity')
            ->select('item, activity')
            ->where('item.identifier = :identifier AND activity.activityId = :activityId')
            ->setParameter('identifier', $identifier)
            ->setParameter('activityId', $activityId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ALL Scorm12 course items from the repository
     *
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function findAllItemsByActivityId ($activityId)
    {
        $repository = $this->itemsRepository();

        // create query
        $qb = $repository->createQueryBuilder('item')
            ->leftJoin('item.activity', 'activity')
            ->select('item, activity')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Create ONE learning activity in repository
     *
     * @param array $data
     * @return Entity\LearningActivity
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

	        // check for cats ( they cause issues when mixing in dogs )
	        if(isset($activity['category_id']) && $activity['category_id']){
	            $category = $this->getEntityManager()->getReference('Savvecentral\Entity\Taxonomy',$activity['category_id']);
	            $activity->setCategory($category);
	        }

	        $activity['scorm12_activity'] = null; // hack as the cascade persist does not work

	        // save in repository
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

			// create the scorm12 activity launch settings
            $scorm12ActivityData = $data['scorm12Activity'];
			$scorm12Activity = new Entity\Scorm12Activity(); // $activity['scorm12_activity'];
			$scorm12Activity = Stdlib\ObjectUtils::hydrate($scorm12ActivityData, $scorm12Activity);
	        $scorm12Activity['activity'] = $activity;
        //    $activityId = $activity['activity_id'];

	        // save the scorm12 activity launch settings
	        $entityManager->persist($scorm12Activity);
			$entityManager->flush($scorm12Activity);

            // save any learning plan reference
            if (isset($planIds)) {
                foreach ($planIds as $planId) {
                    $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
                    if (isset($plan)) {

                        // clear any existing references for this Learning Plan + Activity combination !1 dont need this for the create function
                    /*    $entityManager->createQuery('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.plans = :planId AND learningPlanActivity.activities = :activityId')
                            ->setParameter('planId', $planId)
                            ->setParameter('activityId', $activityId)
                            ->execute(); */

                        // create new entity
                        $lpa = new Entity\LearningPlanActivity();
                        $arr = array('plan_id' => $planId, 'activity_id' => $activity);

                        // set the Plan and Activity entities
                        $arr['plans'] = $plan;
                        $arr['activities'] = $activity;
                        if (isset($data['prerequisite'][$planId])) {
                            $arr['prerequisite'] = $data['prerequisite'][$planId];
                        }
                        $lpa = Stdlib\ObjectUtils::hydrate($arr, $lpa);

                        // save in repository
                        $entityManager->persist($lpa);
                        $entityManager->flush($lpa);
                    }
                }
            }

            // Damn Jedi again!!
			return $activity;
		}
		catch (\Exception $e) {
		    throw $e;
		}
    }

    /**
     * Update ONE learning activity in repository
     *
     * @param Entity\LearningActivity $data
     * @return Entity\LearningActivity
     * @throws \Exception
     */
    public function updateActivity ($data)
    {
        try{
            // extract data
            $data = Stdlib\ObjectUtils::extract($data);

            // get id to get references
            $activityId = $data['activityId'];

            // titty manager
            $entityManager = $this->getEntityManager();

            // if we need a plan
            $planIds = false;
            if (isset($data['planId']) && $data['planId'][0] != '') {
                $planIds = $data['planId'];
                // this needs to be cleared as it wont propagate
                unset($data['planId']);
            }

            /* @var $activity Entity\LearningActivity */
            $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);
            $activity = Stdlib\ObjectUtils::hydrate($data, $activity);

            // check for cats ( they cause issues when mixing in dogs )
            if(isset($activity['category_id']) && $activity['category_id']){
                $category = $this->getEntityManager()->getReference('Savvecentral\Entity\Taxonomy',$activity['category_id']);
                $activity->setCategory($category);
            }
            // forcibly remove the hasPlans if no plans are set
            if (empty($planIds) || (is_array($planIds) && count($planIds) == 0)) {
                $activity->setHasPlans(null);
            }

            // set this to null on prevent dummy spitting - cascade is not setup for this doctrine entity element
            $activity['scorm12_activity'] = null;
            $entityManager->persist($activity);
            $entityManager->flush($activity);

            // get the reference for the child element
            $scorm12ActivityData = $data['scorm12Activity'];
            $scorm12Activity = $entityManager->getReference('Savvecentral\Entity\Scorm12Activity', $activityId);
            $scorm12Activity = Stdlib\ObjectUtils::hydrate($scorm12ActivityData, $scorm12Activity);
            $scorm12Activity['activity'] = $activity;

            // update the child
            $entityManager->persist($scorm12Activity);
            $entityManager->flush($scorm12Activity);

            // save any learning plan reference
            if (isset($planIds) && is_array($planIds)) {
                foreach ($planIds as $planId) {
                    $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
                    if (isset($plan)) {

                        //  clear any existing references for this Learning Plan + Activity combination
                        $entityManager->createQuery('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.plans = :planId AND learningPlanActivity.activities = :activityId')
                            ->setParameter('planId', $planId)
                            ->setParameter('activityId', $activityId)
                            ->execute();

                        // create new entity
                        $lpa = new Entity\LearningPlanActivity();
                        $arr = array('plan_id' => $planId, 'activity_id' => $activity);

                        // set the Plan and Activity entities
                        if (isset($data['prerequisite'][$planId])) {
                            $arr['prerequisite'] = $data['prerequisite'][$planId];
                        }
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

            // of the Jedi??
            return $activity;
        }
        catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * Retrieve the Scorm 1.2 items from the manifest file
     *
     * @param string $manifestFileName
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function retrieveItemsFromManifest ($manifestFileName, $activityId, $cdnUrl, $siteUrl)
    {
        // read the manifest file and extract the scorm12 items
        $manifest = new \Scorm12\Service\ManifestParser($manifestFileName);

        // check if there are items in the manifest
        if (!isset($manifest['items'])) {
            throw new Exception\DomainException('Cannot load the items from manifest file');
        }

        $entityManager = $this->getEntityManager();

        // get the current activity
        /* @var $activity \Savvecentral\Entity\LearningActivity */
        $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);

        $scorm12ItemId = null;
        $scorm12Items  = $activity['scorm12Items'];
        if ($scorm12Items && count($scorm12Items) >= 1) {
            $scorm12ItemId = $scorm12Items[count($scorm12Items)-1]['item_id'];
        }

        // save the scorm12 items
        $items = $manifest['items'];

        /**
         * Due to an anomaly that is causing the Launch button to appear for Completed Scorm12 activities after a new Item has been uploaded -
         *  - we will create an 'Archived Copy' and then just overwrite / update the existing item
         */
        /* @var $activity \Savvecentral\Entity\Scorm12Item */
        if ($scorm12ItemId) {
            $scormItem                          = $entityManager->getReference('Savvecentral\Entity\Scorm12Item', $scorm12ItemId);
            $scorm12ItemHistory                 = new Entity\Scorm12ItemHistory();
            $scorm12ItemHistory                 = Stdlib\ObjectUtils::hydrate($scormItem, $scorm12ItemHistory);
            $scorm12ItemHistory['activity_id']  = $activityId;
            $scorm12ItemHistory['status']       = 'inactive';

            $entityManager->persist($scorm12ItemHistory);
            $entityManager->flush($scorm12ItemHistory);
        }

        // add or update the items from the manifest
        foreach ($items as $item) {
            // skip items without identifierref
            if (!isset($item['identifierref']) || empty($item['identifierref']) || !isset($item['itemlocation']) ||  empty($item['itemlocation'])) {
                continue;
            }

            // create the new CDN based URL for the launch link (item_location) value
            $location =  isset($item['itemlocation']) ? $item['itemlocation'] : 'parent';
            if ($location != 'parent') {
                $itemLocation = $cdnUrl . DIRECTORY_SEPARATOR . $siteUrl . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . $activityId . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . $location;
            }

            // if already in the repository, update - used when re-uploading a scorm12 package
            if ($scorm12ItemId) {
                $scormItem = Stdlib\ObjectUtils::hydrate($item, $scormItem);
                $scormItem['status'] = 'active';
                $scormItem['item_location'] = $itemLocation;

            }
            else {
                // create new scorm12 item doctrine entity - when a new activity has been created
                $scormItem = new Entity\Scorm12Item();
                $scormItem['title'] = $item['title'];
                $scormItem['identifier'] = $item['identifier'];

                $scormItem['is_visible'] = isset($item['isvisible']) ? $item['isvisible'] : 1;
                $scormItem['max_time_allowed'] = isset($item['maxtimeallowed']) ? $item['maxtimeallowed'] : null;
                $scormItem['prerequisites'] = isset($item['prerequisites']) ? $item['prerequisites'] : null;
                $scormItem['time_limit_action'] = isset($item['timelimitaction']) ? Stdlib\StringUtils::stringReplaceRegex('[,\s\s]+', ' ', $item['timelimitaction']) : null;
                $scormItem['data_from_lms'] = isset($item['datafromlms']) ? $item['datafromlms'] : null;
                $scormItem['mastery_score'] = isset($item['masteryscore']) ? $item['masteryscore'] : null;
                $scormItem['item_location'] = $itemLocation;
                $scormItem['status'] = 'active';

                // associate with the current activity
                $scormItem['activity'] = $activity;
            }

            // persist the new item into doctrine
            $entityManager->persist($scormItem);
            $entityManager->flush($scormItem);
        }

        // return the 'scorm12 item' as an array instead of the activity
        return array($scormItem);
    }

    /**
     * Update ONE Scorm12 item
     *
     * @param Entity\Scorm12Item $data
     * @return Entity\Scorm12Item
     * @throws \Exception
     */
    public function updateItem ($data)
    {
        try {
	        $entityManager = $this->getEntityManager();

            // extract the data
            $data = Stdlib\ObjectUtils::extract($data);

            // get the current item
            $itemId = $data['itemId'];
            /* @var $activity \Savvecentral\Entity\Scorm12Item */
            $item = $entityManager->getReference('Savvecentral\Entity\Scorm12Item', $itemId);
            $item = Stdlib\ObjectUtils::hydrate($data, $item);

	        // save in repository
	        $entityManager->persist($item);
	        $entityManager->flush($item);

	        return $item;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE item by item ID
     *
     * @param integer $itemId
     */
    public function deleteItem ($itemId)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // retrieve the item
	        $repository = $this->itemsRepository();
	        $item = $repository->findOneByItemId($itemId);
	        $item['status'] = 'deleted';

	        // save in repository
	        $entityManager->persist($item);
	        $entityManager->flush($item);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Decompress the Scorm12 course archive
     *
     * @param string $filename The file path of the zip archive to decompress
     * @param string $destinationPath The destination path where to extract the zip contents
     */
    public function decompressArchive ($filename, $destinationPath)
    {
        Stdlib\FileUtils::makeDirectory($destinationPath);
        $filter = new \Zend\Filter\Decompress([
            'adapter' => 'Zip',
            'options' => [
                'target' => $destinationPath
            ]
        ]);
        $filter->filter($filename);
    }

    /**
     * Get the mod_rewrite rules for the scorm12 files
     *
     * @return string
     */
    public function modRewriteRules ()
    {
        $rule = [];
        $rule[] = '# Deny access to Zip|Rar|7z files';
        $rule[] = '<Files ~ "\.(zip|rar|7z)$">';
        $rule[] = 'deny from all';
        $rule[] = '</Files>';
        return $rule;
    }

    /**
     * Create htaccess rules
     *
     * @param string $filePath Directory path where to store the htaccess file
     * @return \Scorm12\Service\Scorm12Service
     */
    public function createHtaccessRules ($filePath)
    {
        // check if server allows mod_rewrite
        if (Stdlib\ServerUtils::gotModRewrite()) {
            $rules = $this->modRewriteRules();
            $htaccessFilename = $filePath . DIRECTORY_SEPARATOR . '.htaccess';
            Stdlib\ServerUtils::insertHtaccessFile($htaccessFilename, 'SCORM 1.2 LEARNING', $rules);
        }
        return $this;
    }

    private function folderSize($path) {
        $total_size = 0;
        $files = scandir($path);

        foreach($files as $t) {
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
                if ($t<>"." && $t<>"..") {
                    $size = $this->folderSize(rtrim($path, '/') . '/' . $t);
                    $total_size += $size;
                }
            } else {
                $size = filesize(rtrim($path, '/') . '/' . $t);
                $total_size += $size;
            }
        }
        return $total_size;
    }

    public function checkFilespaceUsage($siteId)
    {
        if ($siteId) {
            // get the site entity instance
            $entityManager = $this->getEntityManager();
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
            $url = $site['url'];

            $public = CLIENT_PATH . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . 'public';
            $public = realpath($public);

            $publicStorageUsed = 0;
            if (file_exists($public)) {
                $publicStorageUsed = $this->folderSize($public);
            }

            $cdn = MODULE_CDN_PATH . DIRECTORY_SEPARATOR . $url;
            $cdn = realpath($cdn);

            $cdnStorageUsed = 0;
            if (file_exists($cdn)) {
                $cdnStorageUsed = $this->folderSize($cdn);
            }

            return ($publicStorageUsed + $cdnStorageUsed);


        }
        return 0;
    }

    /**
     * Get the scorm12 items doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function itemsRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Scorm12Item');
    }

    public function patchTracking() {

        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\TrackerActivity');

        $activityId       = 550693;
        $completionStatus = 'completed';
        $completedOn      = '2018-01001 00:00:00';

        $itemId           = 550854;

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.distribution', 'distribution')
            ->leftJoin('distribution.trackerItem', 'trackerScorm12Items')

            ->select('activity, distribution, trackerScorm12Items')
            ->where('distribution.activity = :activityId')
            ->andWhere('activity.completionStatus = :completionStatus')
            ->andWhere('activity.completedOn > :completedOn')
            ->andWhere('trackerScorm12Items.distribution IS NULL')
            ->setParameter('activityId', $activityId)
            ->setParameter('completionStatus', $completionStatus)
            ->setParameter('completedOn', $completedOn);

        // execute query
        $results = $repository->fetchCollection($qb);

        foreach ($results as $result) {

            //var_dump($result);
            //die;

            $scormTrackerItem = new Entity\TrackerItem();

            $item = $entityManager->getReference('Savvecentral\Entity\Scorm12Item', $itemId);

            $scormTrackerItem['item'] =  $item;

            $distribution = $entityManager->getReference('Savvecentral\Entity\Distribution', $result['distribution']['distribution_id']);
            $scormTrackerItem['distribution'] = $distribution;

            $scormTrackerItem['completionStatus'] = $result['completion_status'];

            $scormTrackerItem['attemptStatus'] = 'active';

            $scormTrackerItem['score'] = $result['score'];

            $scormTrackerItem['suspendData'] = 1;

            $scormTrackerItem['startedOn'] = $result['started_on'];

            $scormTrackerItem['completedOn'] = $result['completed_on'];

            $scormTrackerItem['lastAccessed'] = $result['last_accessed'];

            // save in repository
            $entityManager->persist($scormTrackerItem);
            $entityManager->flush($scormTrackerItem);

        }

     //   var_dump(count($results));
     //   die(" count displayed");
    }
}
