<?php

namespace Tincan\Service;

use Learning\Service\LearningService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\ArrayCollection;

class TincanService extends AbstractService
{

    /**
     * Find ONE Tincan activity by activity ID
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
            ->leftJoin('activity.tincanItems', 'items')
            ->leftJoin('activity.tincanActivity', 'tincanActivity')
            ->leftJoin('activity.hasPlans', 'plan')
            ->select('activity, site, items, tincanActivity, plan')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE Tincan activity its item's IRI identifier
     *
     * @param string $iri
     * @return Entity\LearningActivity
     */
    public function findOneLearningActivityByIri ($iri)
    {
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.tincanItems', 'item')
            ->select('activity, item')
            ->where('item.itemIri = :iri')
            ->setParameter('iri', $iri);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE Tincan course item from the repository
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
     * @return Entity\TincanItem
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
     * Find ALL Tincan course items from the repository
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

	        // check for cats ( they cause issues when mixing in dogs )
	        if(isset($activity['category_id']) && $activity['category_id']){
	            $category = $this->getEntityManager()->getReference('Savvecentral\Entity\Taxonomy',$activity['category_id']);
	            $activity->setCategory($category);
	        }

	        $activity['tincan_activity'] = null; // hack as the cascade persist does not work

	        // save in repository
	        $entityManager->persist($activity);
	        $entityManager->flush($activity);

			// create the Tincan activity launch settings
            $tincanActivityData = $data['tincanActivity'];
			$tincanActivity = new Entity\tincanActivity();
			$tincanActivity = Stdlib\ObjectUtils::hydrate($tincanActivityData, $tincanActivity);
	        $tincanActivity['activity'] = $activity;
        //    $activityId = $activity['activity_id'];

	        // save the Tincan activity launch settings
	        $entityManager->persist($tincanActivity);
			$entityManager->flush($tincanActivity);

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

            // get the item related data
            $tincanActivityData = $data['tincanActivity'];

            // set this to null on prevent dummy spitting - cascade is not setup for this doctrine entity element
            $activity['tincan_activity'] = null;
            $entityManager->persist($activity);
            $entityManager->flush($activity);

            // get the reference for the child element
            $tincanActivity = $entityManager->getReference('Savvecentral\Entity\TincanActivity', $activityId);
            $tincanActivity = Stdlib\ObjectUtils::hydrate($tincanActivityData, $tincanActivity);
            $tincanActivity['activity'] = $activity;

            // update the child
            $entityManager->persist($tincanActivity);
            $entityManager->flush($tincanActivity);

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
     * Retrieve the Tincan items from the manifest file
     *
     * @param $manifestFileName
     * @param $activityId
     * @param $cdnUrl
     * @param $siteUrl
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function retrieveItemsFromManifest ($manifestFileName, $activityId, $cdnUrl, $siteUrl)
    {
        // read the manifest file and extract the tincan items
        $manifest = new \Tincan\Service\ManifestParser($manifestFileName);

        // check if there are items in the manifest
        if (!isset($manifest['items'])) {
            throw new Exception\DomainException('Cannot load the items from manifest file');
        }

        $entityManager = $this->getEntityManager();

        // get the current activity
        /* @var $activity \Savvecentral\Entity\LearningActivity */
        $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);
        $activityDescription = false;

        // save the primary Tincan item (master activity)
        $items = $manifest['items'];

        // get all the activities found in the manifest
        $activities = $manifest['activities']['activity'];

        // extract the identifier from the array of items - there will only ever be one as were not using multiple items as yet
        $identifiers = Stdlib\ArrayUtils::extractColumnValues($items, 'type');

        // delete items not in the new items
        $entityManager->createQuery("UPDATE Savvecentral\Entity\TincanItem item SET item.status = :status WHERE item.identifier NOT IN(:identifier) AND item.activity = :activityId")
        	->setParameter('status', 'inactive')
        	->setParameter('identifier', $identifiers)
        	->setParameter('activityId', $activityId)
        	->execute();

        // add or update the items from the manifest
        foreach ($items as $item) {
            // skip items without a name or location
            if (!isset($item['name']) || empty($item['name']) || !isset($item['itemlocation']) ||  empty($item['itemlocation'])) {
                continue;
            }

            // set the Activity description if its found
            $activityDescription = isset($item['description']) && is_string($item['description']) ? '<p>'. $item['description'] . '</p>' : null;

            // use the 'type' specified in the tincan.xml as the <activity> attribute
            $identifier = $item['type'];

            // check if item is in the repository
            $found = $this->findOneItemByIdentifier($identifier, $activityId);

            // create the new CDN based URL for the launch link (item_location) value
            $location =  isset($item['itemlocation']) ? $item['itemlocation'] : 'parent';
            $itemLocation = false;
            if ($location != 'parent') {
                $itemLocation = $cdnUrl . DIRECTORY_SEPARATOR . $siteUrl . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . $activityId . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . $location;
            }

            // prepare the site 'homePage' definition - each site needs to query the LRS using a unique 'homePage' value for the Actor
            $protocol = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == true)) ? 'https' : 'http');
            $homePage = $protocol . '://' . $siteUrl;

            $iri = $item['id'];
            $subActivities = array();
            // extract the modules (sub-activities) from the array of activities if there are more than one
            if (!isset($activities['id']) && count($activities) >= 2) {
                $index = 0;
                foreach ($activities as $module) {
                    // ignore the first one
                    if ($index == 0) { $index++; continue; }

                    $id = $module['id'];
                    $subActivities[] = str_replace($iri . '/', '', $id);
                }
            }
            // we want to store these as a string (comma delimited)
            $subActivities = implode(",", $subActivities);

            // if already in the repository, update
            if ($found) {
                $tincanItem = Stdlib\ObjectUtils::hydrate($item, $found);
                $tincanItem['status'] = 'active';
                $tincanItem['item_iri'] = $iri;
                $tincanItem['item_activities']  = $subActivities;
                $tincanItem['item_location'] = $itemLocation;
            }

            // else, create new
            else {
                // create new tincan item doctrine entity
                $tincanItem = new Entity\TincanItem();
                $tincanItem['title']            = $item['name'];
                $tincanItem['identifier']       = $identifier;
                $tincanItem['item_activities']  = $subActivities;
                $tincanItem['is_visible']       = isset($item['isvisible']) ? $item['isvisible'] : 1;
                $tincanItem['max_time_allowed'] = isset($item['maxtimeallowed']) ? $item['maxtimeallowed'] : null;
                $tincanItem['prerequisites']    = isset($item['prerequisites']) ? $item['prerequisites'] : null;
                $tincanItem['time_limit_action'] = isset($item['timelimitaction']) ? Stdlib\StringUtils::stringReplaceRegex('[,\s\s]+', ' ', $item['timelimitaction']) : null;
                $tincanItem['data_from_lms']    = isset($item['datafromlms']) ? $item['datafromlms'] : null;
                $tincanItem['mastery_score']    = isset($item['masteryscore']) ? $item['masteryscore'] : null;
                $tincanItem['item_location']    = $itemLocation;
                $tincanItem['item_iri']         = $iri;
                $tincanItem['item_homepage']    = $homePage;
                $tincanItem['status']           = 'active';

                // associate with the current activity
                $tincanItem['activity'] = $activity;
            }

            // persist the new item into doctrine
            $entityManager->persist($tincanItem);
            $entityManager->flush($tincanItem);
        }

        // save the 'activityDescription if set
        if ($activityDescription) {
            $description = $activity['description'];
            // concat the descriptions
            $description .= $activityDescription;
            $activity['description'] = $description;

            // persist the activity into doctrine
            $entityManager->persist($activity);
            $entityManager->flush($activity);
        }

        // return the 'tincan item' as an array instead of the activity
        return array($tincanItem);
    }

    /**
     * Update ONE Tincan item
     *
     * @param Entity\TincanItem $data
     * @return Entity\TincanItem
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
            /* @var $activity \Savvecentral\Entity\TincanItem */
            $item = $entityManager->getReference('Savvecentral\Entity\TincanItem', $itemId);
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
     * Decompress the Timncan course archive
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
     * Get the mod_rewrite rules for the Tincan files
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
            Stdlib\ServerUtils::insertHtaccessFile($htaccessFilename, 'TINCAN LEARNING', $rules);
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
    }

    /**
     * Get the Tincan items doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function itemsRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\TincanItem');
        return $repository;
    }
}
