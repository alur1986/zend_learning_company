<?php

namespace Group\Learner\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class GroupLearnerService extends AbstractService
{

    /**
     * Find ONE learner by user ID
     *
     * @param integer $userId
     * @return Entity\Learner
     */
    public function findOneLearnerById ($userId)
    {
        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner, groupLearners, groups, grpLearners, grpLearner')
            ->leftJoin('learner.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.group', 'groups')
            ->leftJoin('groups.groupLearners', 'grpLearners')
            ->leftJoin('grpLearners.learner', 'grpLearner')
            ->where('learner.userId = :userId')
            ->andWhere('learner.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', 'active')
            ->add('orderBy', 'groups.name ASC, grpLearner.firstName ASC');

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE group by group ID
     *
     * @param integer $groupId
     * @return Entity\Groups
     */
    public function findOneGroupById ($groupId)
    {
        $learnerStatus = [ 'new', 'active' ];
        $repository = $this->groupRepository();

        // create query
        $qb = $repository->createQueryBuilder('groups')
            ->leftJoin('groups.site', 'site')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.learner', 'learner', 'WITH' , 'learner.status IN (:learnerStatus)')
            ->leftJoin('learner.employment', 'employment')
            ->leftJoin('learner.settings', 'settings')
            ->select('groups, site, groupLearners, learner, employment, settings, learner.status as learner_active')
            ->where('groups.groupId = :groupId')
            ->setParameter('groupId', $groupId)
            ->setParameter('learnerStatus', $learnerStatus)
            ->add('orderBy', 'groups.name ASC, learner.firstName ASC');

        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find all learners that are NOT in the provided group and are active
     *
     * @param integer $groupId
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function getLearnersNotInGroup($groupId, $siteId = null)
    {
    	$learnerStatus = [ 'new', 'active' ];

    	$repository = $this->learnerRepository();
    	// get all active learners in site
    	$qb = $repository->createQueryBuilder('learners')
    	->select('learners.userId as learner_id, learners.firstName as learner_firstname, learners.lastName as learner_lastname, learners.email as learner_email, learners.telephone as learner_telephone, learners.status as learner_status')
    	->where('learners.site = :siteId AND learners.status IN (:learnerStatus)')
    	->setParameter('siteId', $siteId)
    	->setParameter('learnerStatus', $learnerStatus)
    	->add('orderBy', 'learners.firstName ASC');

    	$learners = $repository->fetchCollection($qb);

    	$repository = $this->groupLearnerRepository();
    	// get all learners in group
    	$qb = $repository->createQueryBuilder('groupLearner')
    	->select('groupLearner')
    	->where('groupLearner.group = :groupId')
    	->setParameter('groupId', $groupId)
    	->add('orderBy', 'groupLearner.id ASC');

    	$groupLearners = $repository->fetchCollection($qb);
    	$guid = array();
    	foreach ($groupLearners as $group) {
    		$guid[] = $group['learner']['userId'];
    	}

    	$arr = array();
    	foreach($learners as $learner) {
    		if (in_array($learner['learner_id'], $guid)) continue;
    		$arr[] = $learner;
    	}
    	$results = $arr;

    	$repository = $this->groupRepository();
    	// get group name
    	$qb = $repository->createQueryBuilder('groups')
    	->select('groups.name')
    	->where('groups.groupId = :groupId')
    	->setParameter('groupId', $groupId);

    	$groupName = $repository->fetchOne($qb);
    	$results['name'] = $groupName['name'];
    	return $results;
    }

    /**
     * Find ALL learners within a group
     *
     * @param integer $groupId
     * @return ArrayCollection
     */
    public function findAllLearnersInGroupId ($groupId)
    {
        $learnerStatus = [ 'new', 'active' ];
        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner, groupLearners, groups')
        	->leftJoin('learner.groupLearners', 'groupLearners')
        	->leftJoin('groupLearners.group', 'groups')
        	->where('groups.groupId = :groupId AND learner.status IN (:learnerStatus)')
        	->setParameter('groupId', $groupId)
        	->setParameter('learnerStatus', $learnerStatus)
        	->add('orderBy', 'learner.firstName ASC, learner.lastName ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL learners within a group
     *
     * @param integer $groupId
     * @return ArrayCollection
     */
    public function findAllLearnersCurrentlyInGroup ($groupId)
    {
    	$learnerStatus = [ 'new', 'active', 'inactive' ];
    	$repository = $this->learnerRepository();

    	// create query
    	$qb = $repository->createQueryBuilder('learner')
    	->select('learner, groupLearners, groups')
    	->leftJoin('learner.groupLearners', 'groupLearners')
    	->leftJoin('groupLearners.group', 'groups')
    	->where('groups.groupId = :groupId AND learner.status IN (:learnerStatus)')
    	->setParameter('groupId', $groupId)
    	->setParameter('learnerStatus', $learnerStatus)
    	->add('orderBy', 'learner.status ASC, groupLearners.role ASC, learner.firstName ASC, learner.lastName ASC');

    	// execute query
    	$results = $repository->fetchCollection($qb);

    	return $results;
    }

    /**
     * Get the Group name by GroupId
     */
    public function getGroupNameByGroupId ($groupId)
    {
    	$repository = $this->groupRepository();
    	// get group name
    	$qb = $repository->createQueryBuilder('groups')
    	->select('groups.name')
    	->where('groups.groupId = :groupId')
    	->setParameter('groupId', $groupId);

    	$groupName = $repository->fetchOne($qb);
    	return $groupName['name'];
    }

    /**
     * Find ALL ADMIN learners within a group
     *
     * @param integer $groupId
     * @return ArrayCollection
     */
    public function findAllAdminsInGroupId ($groupId)
    {
        $role = 'admin';
        $learners = $this->findAllLearnersInGroupId($groupId);

        // filter the learner collection
        $expr = Criteria::expr();
        $criteria = new Criteria();
        $criteria->where($expr->eq('role', $role));
        $admins = $learners->matching($criteria);

        return $admins;
    }

    /**
     * Fetch ALL learners within ONE group
     *
     * @param integer $groupId
     * @param array   $learnerStatus
     * @return array
     */
    public function fetchAllLearnersInGroupId ($groupId, $learnerStatus = ['new','active'])
    {
        $groupId = (array) $groupId;
        $entityManager = $this->getEntityManager();
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT
            learner.userId AS user_id,
            learner.firstName AS first_name,
            learner.lastName AS last_name,
            CONCAT(learner.firstName,' ', learner.lastName) AS name,
            learner.telephone,
            learner.email,
            groupLearners.role,
            groupLearners.status,
            CASE WHEN groupLearners.role = 'admin' THEN 10 ELSE 0 END AS sort
            FROM Savvecentral\Entity\Learner learner
            LEFT JOIN learner.groupLearners groupLearners
            LEFT JOIN groupLearners.group groups
            WHERE groups.groupId IN(:groupId) AND learner.status IN (:learnerStatus)
            ORDER BY sort DESC, learner.firstName ASC, learner.lastName ASC";

        // execute query
        $results = $entityManager->createQuery($dql)
            ->setParameter('groupId', $groupId)
            ->setParameter('learnerStatus', $learnerStatus)
            ->getArrayResult();

        return $results;
    }

    /**
     * Fetch ALL of THE learners within ONE group (added 'inactive' - just in case)
     *
     * @param integer $groupId
     * @param array   $learnerStatus
     * @return array
     */
    public function fetchAllTheLearnersInGroupId ($groupId, $learnerStatus = ['new','active','inactive'])
    {
        $groupId = (array) $groupId;
        $entityManager = $this->getEntityManager();
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT
                learner.userId AS user_id,
                learner.firstName AS first_name,
                learner.lastName AS last_name,
                CONCAT(learner.firstName,' ', learner.lastName) AS name,
                learner.telephone,
                learner.email,
                groupLearners.role,
                learner.status,
                CASE WHEN groupLearners.role = 'admin' THEN 10 ELSE 0 END AS sort
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                WHERE groups.groupId IN(:groupId) AND learner.status IN (:learnerStatus)
                GROUP BY learner.userId
                ORDER BY learner.firstName ASC, learner.lastName ASC";

        // execute query
        $results = $entityManager->createQuery($dql)
            ->setParameter('groupId', $groupId)
            ->setParameter('learnerStatus', $learnerStatus)
            ->getArrayResult();

        return $results;
    }

    /**
     * Find ALL groups by learner ID
     *
     * @param integer $learnerId
     * @return ArrayCollection
     */
    public function findAllGroupsByLearnerId ($learnerId)
    {
        $repository = $this->groupRepository();

        // create query
        $qb = $repository->createQueryBuilder('groups')
            ->select('groups, groupLearners, learner')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.learner', 'learner')
            ->where('learner.userId = :userId')
            ->setParameter('userId', $learnerId)
            ->add('orderBy', 'groups.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL groups where the learner has the ADMIN role
     *
     * @param integer $learnerId
     * @return ArrayCollection
     */
    public function findAllGroupsByAdmin ($learnerId)
    {
        $role = 'admin';
        $repository = $this->groupRepository();

        // create query
        $qb = $repository->createQueryBuilder('groups')
            ->select('groups, groupLearners, learner')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.learner', 'learner')
            ->where('learner.userId = :userId AND groupLearners.role = :role')
            ->setParameter('userId', $learnerId)
            ->setParameter('role', $role)
            ->add('orderBy', 'groups.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL members of groups within the group admin's managed group
     *
     * @param integer $userId Group Admin's user ID
     * @return ArrayCollection
     */
    public function findAllLearnersByGroupAdmin ($userId, array $activityIds = [])
    {
        $role = 'admin';
        $learnerStatus = [ 'new', 'active', 'inactive' ];

        $entityManager = $this->getEntityManager();
        $repository = $this->learnerRepository();

        // filter by groups that this user has a role as admin
        // get all the group IDs that this learner belongs to and is admin
        $qbGl = $entityManager->createQueryBuilder()
            ->from('Savvecentral\Entity\GroupLearners', 'glr')
            ->leftJoin('glr.group', 'grp')
            ->select('grp.groupId')
            ->where('glr.learner = :userId AND glr.role = :role')
            ->setParameter('userId', $userId)
            ->setParameter('role', $role);

        $qbActivities = $entityManager->createQueryBuilder()
            ->from('Savvecentral\Entity\Distribution', 'distribution')
            ->select('dLearner.userId')
            ->join('distribution.learner', 'dLearner')
            ->where('distribution.activity in (:activityIds)')
            ->setParameter('activityIds', $activityIds);

        // create query
        $qb = $repository->createQueryBuilder('learner');
        $qb->leftJoin('learner.site', 'site')
            ->leftJoin('learner.employment', 'employment')
            ->leftJoin('learner.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.group', 'groups')
            ->select('learner, site, employment, groupLearners, partial groups.{groupId,name}')
            ->where('learner.status IN (:learnerStatus)')
            ->setParameter('learnerStatus', $learnerStatus)
            ->andWhere($qb->expr()
            ->in('groups.groupId', $qbGl->getDQL()))
            ->setParameter('userId', $userId)
            ->setParameter('role', $role)
            ->add('groupBy', 'learner.userId')
            ->add('orderBy', 'learner.firstName ASC, learner.lastName ASC');

        if (count($activityIds) > 0) {
            $qb
                ->andWhere(
                    $qb->expr()->in('learner.userId', $qbActivities->getDQL())
                )
                ->setParameter('activityIds', $activityIds);
        }

        // execute query
        $learners = $repository->fetchCollection($qb);

        return $learners;
    }

	/**
	 * Add a set of learners to groups
	 * @param array $learnerIds
	 * @param array $groupIds
	 * @param array $badLearners
	 *
	 * @throws \Exception
	 */
    public function addLearnersToGroups (array $learnerIds, array $groupIds, array $badLearners = [])
    {
        try {
            $entityManager = $this->getEntityManager();
            $repository = $this->groupLearnerRepository();

            // check if the learners are already in the groups
            $qb = $repository->createQueryBuilder('groupLearners')
                ->select('groupLearners')
                ->where('groupLearners.learner IN (:learnerIds) AND groupLearners.group IN (:groupIds)')
                ->setParameter('learnerIds', $learnerIds)
                ->setParameter('groupIds', $groupIds);
            $results = $repository->fetchCollection($qb);

            // add each learner to the groups
            foreach ($learnerIds as $learnerId) {
                foreach ($groupIds as $groupId) {
                    $groupLearner = $results->filter(function  ($item) use( $learnerId, $groupId) { return $item['learner']['user_id'] == $learnerId && $item['group']['group_id'] == $groupId; })->current();


                    // if not in the group learners repository, this is an update
                    if (!$groupLearner) {
                        $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);
                        $group = $entityManager->getReference('Savvecentral\Entity\Groups', $groupId);

                        // create new entity
                        $groupLearner = new Entity\GroupLearners();
                        $groupLearner['learner'] = $learner;
                        $groupLearner['group'] = $group;
                        $groupLearner['role'] = 'learner';
                    }
                    $groupLearner['status'] = 'active';

                    // save in repository
                    $entityManager->persist($groupLearner);
                    $entityManager->flush($groupLearner);
                }
            }
            if (count($badLearners) >= 1) {
                $message = "CSV Uploaded with " . count($badLearners) . " error(s)! Unable to update/add the following learner(s)-> ";
                foreach ($badLearners as $bl) {
                    $message .= " Learner:";
                    foreach ($bl as $el) {
                        $message .= " " . $el;
                    }
                    $message .= "....";
                }
                throw new \Exception(sprintf("%s", $message));
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *  Set the learners as LEARNER role
     *
     * @param array $learnerIds
     * @param array $groupIds
     * @return $this
     * @throws \Exception
     */
    public function setLearnersAsLearnerInGroups (array $learnerIds, array $groupIds)
    {
        try {
	        $role = 'learner';
	        $entityManager = $this->getEntityManager();
	        $repository = $this->groupLearnerRepository();

	        // query
	        $results = $repository->createQueryBuilder('groupLearners')
	            ->update('Savvecentral\Entity\GroupLearners', 'groupLearners')
	            ->set('groupLearners.role', ':role')
	            ->where('groupLearners.learner IN (:learnerIds) AND groupLearners.group IN (:groupIds)')
	            ->setParameter('learnerIds', $learnerIds)
	            ->setParameter('groupIds', $groupIds)
	            ->setParameter('role', $role)
	            ->getQuery()
	            ->execute();
	        $entityManager->clear();

	        return $this;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the learners as ADMIN role
     *
     * @param array $learnerIds
     * @param array $groupIds
     * @return $this
     * @throws \Exception
     */
    public function setLearnersAsAdminInGroups (array $learnerIds, array $groupIds)
    {
        try {
	        $role = 'admin';
	        $entityManager = $this->getEntityManager();
	        $repository = $this->groupLearnerRepository();

	        // query
	        $results = $repository->createQueryBuilder('groupLearners')
	            ->update('Savvecentral\Entity\GroupLearners', 'groupLearners')
	            ->set('groupLearners.role', ':role')
	            ->where('groupLearners.learner IN (:learnerIds) AND groupLearners.group IN (:groupIds)')
	            ->setParameter('learnerIds', $learnerIds)
	            ->setParameter('groupIds', $groupIds)
	            ->setParameter('role', $role)
	            ->getQuery()
	            ->execute();
	        $entityManager->clear();

	        return $this;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove learners from the group
     *
     * @param array $learnerIds
     * @param array $groupIds
     * @throws \Exception
     */
    public function removeLearnersFromGroups (array $learnerIds, array $groupIds)
    {
        try {
	        $entityManager = $this->getEntityManager();
	        $repository = $this->groupLearnerRepository();

	        // create query
	        $results = $repository->createQueryBuilder('groupLearners')
	            ->delete('Savvecentral\Entity\GroupLearners', 'groupLearners')
	            ->where('groupLearners.learner IN (:learnerIds) AND groupLearners.group IN (:groupIds)')
	            ->setParameter('learnerIds', $learnerIds)
	            ->setParameter('groupIds', $groupIds)
	            ->getQuery()
	            ->execute();
	        $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add learners to the group using CSV array data
     *
     * @param array $data
     * @param array $groupIds
     * @throws \Exception
     * @return boolean
     */
    public function importLearnersFromCsv (array $data, array $groupIds)
    {
        try {
            $entityManager = $this->getEntityManager();

            // convert each item's row heading to underscore format
            $data = Stdlib\ArrayUtils::underscoreFieldNormaliseValue($data);
            // catch empty uploads and return an exception that can be used to return a nice message
            $totalUploadedData = count($data);
            if ($totalUploadedData == 1 || $totalUploadedData == 0) {
                $item = (isset($data[0])) ? $data[0] : false;
                if (!(isset($item['email']) && strlen($item['email']) >= 5) && !(isset($item['mobile_number']) && strlen($item['mobile_number']) >= 8) && !(isset($item['employment_id']) && strlen($item['employment_id']) >= 1)) {
                    throw new \Exception("Empty CSV Uploaded");
                }
            }

            // only learners with the following status can be added to groups
            $learnerStatus = [ 'new', 'active' ];

            // get the current site ID
            $routeMatch = $this->routeMatch();
            $siteId = $routeMatch->getParam('site_id');

            $dql = "SELECT
                    learner.userId AS learner_id,
                    learner.email AS email,
                    learner.mobileNumber AS mobile_number,
                    employment.employmentId AS employment_id

                    FROM Savvecentral\Entity\Learner learner
                    LEFT JOIN learner.site site
                    LEFT JOIN learner.employment employment
                    WHERE site.siteId = :siteId AND learner.status IN (:learnerStatus)
                    ORDER BY learner.firstName, learner.lastName";
            $params['siteId'] = $siteId;
            $params['learnerStatus'] = $learnerStatus;
            $query = $entityManager->createQuery($dql)
            ->setParameters($params);
            $learners = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);

            $learnerIds = array();
            $badLearners = array();
            // search the results to build the list of learner ID's
            foreach ($data as $item) {
                // check if the current item exists in the learner repository
               $found = array_filter($learners, function  ($a) use( $item)
                {
                    return ((isset($item['email']) && $item['email']) && (isset($a['email']) && $a['email']) && trim($item['email']) === trim($a['email']))
                    || ((isset($item['mobile_number']) && $item['mobile_number']) && (isset($a['mobile_number']) && $a['mobile_number']) && trim($item['mobile_number']) === trim($a['mobile_number']))
                    || ((isset($item['employment_id']) && $item['employment_id']) && (isset($a['employment_id']) && $a['employment_id']) && trim($item['employment_id']) === trim($a['employment_id']));

                    return false;
                });
               if ($found && count($found) >= 1) {
                   foreach($found as $arr) $learner = $arr;
                   $learnerIds[] = $learner['learner_id'];
               } else {
                   // pass this to the 'addLearnersToGroups' method to return as an Exception if it contains data
                   $badLearners[] = $item;
               }
            }

            // if none found, throw an error
            if (!count($learnerIds)) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot add learners to the group. Could not find any of these learners in the database.'), null, null);
            }
            // add learners to the groups
            return $this->addLearnersToGroups($learnerIds, $groupIds, $badLearners);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the group learners doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function groupLearnerRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\GroupLearners');
        return $repository;
    }

    /**
     * Get the groups doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function groupRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Groups');
        return $repository;
    }

    /**
     * Get the learners doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learnerRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');
        return $repository;
    }

    /**
     * @see \Learning\Factory\Service\LearningActivitiesServiceFactory::createService()
     */
    public function getActivitiesRelatedToGroupAdmin($groupAdminId): array
    {

        $siteId = null;

        $dqlGroupIds = "
        SELECT 
            groups1.groupId
        FROM 
            Savvecentral\Entity\GroupLearners groupLearner1 
        JOIN groupLearner1.group groups1
        WHERE groupLearner1.role = 'admin' AND groupLearner1.learner = :groupAdminId";

        $dqlGroupUserIds = "
            SELECT learner2.userId
            FROM Savvecentral\Entity\GroupLearners as groupLearner2
            JOIN groupLearner2.learner learner2
            WHERE groupLearner2.group IN (".$dqlGroupIds.")
            ";


        $routeMatch = $this->getServiceLocator()->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && ($siteId = $routeMatch->getParam('site_id')))) {
            return [];
        }

        $dqlActivities = "
            SELECT activity.activityId   AS value,
                   activity.title         AS label,
                   activity.activityId   AS activity_id,
                   activity.title         AS title,
                   activity.description   AS description,
                   activity.activityType AS activity_type,
                   activity.status        AS status
            FROM Savvecentral\Entity\Distribution distribution
            LEFT JOIN distribution.activity activity
            LEFT JOIN activity.site site
            WHERE distribution.learner IN (".$dqlGroupUserIds.") AND activity.site = :siteId AND activity.status NOT IN ('deleted')
            GROUP BY activity_id
            ORDER BY activity.title ASC
        ";

        $dqlNumLearners = "
            SELECT  activity.activityId AS activity_id,
                    COUNT(learner.userId) AS num_learners
            FROM Savvecentral\Entity\Learner learner
            JOIN learner.distribution distribution
            JOIN distribution.activity activity
            WHERE distribution.status NOT IN ('deleted', 'disapproved', 'enrolled')
            AND activity.site = :siteId
            AND activity.status NOT IN ('deleted')
            AND learner.status IN ('new', 'active')
            AND learner.userId IN (".$dqlGroupUserIds.")
            GROUP BY activity.activityId   
        ";

        $params = [
            'groupAdminId' => $groupAdminId,
            'siteId' => $siteId,
        ];

        // get activities
        $query = $this
            ->getEntityManager()
            ->createQuery($dqlActivities)
            ->setParameters($params);

        $activities = $query->getArrayResult();
        $activitiesMap = [];
        $activitiesIds = [];
        foreach ($activities as $activity) {
            $activitiesMap[$activity['value']] = array_merge(
                $activity, [
                    'num_learners' => 0,
                    'num_events' => 0
            ]);

            $activitiesIds[] = $activity['value'];
        }

        if (count($activitiesMap) === 0) {
            return [];
        }

        // get num_learners
        $query = $this
            ->getEntityManager()
            ->createQuery($dqlNumLearners)
            ->setParameters($params);

        $numLearners = $query->getArrayResult();
        foreach ($numLearners as $activityNumLearners) {
            $activityKey = $activityNumLearners['activity_id'];
            if (isset($activitiesMap[$activityKey])) {
                $activitiesMap[$activityKey]['num_learners'] = (int) $activityNumLearners['num_learners'];
            }
        }

        $dqlNumEvents = "
            SELECT  eventActivity.activityId,
                    COUNT(event.eventId) AS num_events
            FROM Savvecentral\Entity\Event event
            LEFT JOIN event.activity eventActivity
            WHERE eventActivity.activityId IN (:activitiesIds)
            GROUP BY eventActivity.activityId   
        ";

        // get num_events
        $query = $this
            ->getEntityManager()
            ->createQuery($dqlNumEvents)
            ->setParameters(
                ['activitiesIds' => $activitiesIds,]
            );

        $numEvents = $query->getArrayResult();
        foreach ($numEvents as $activityNumEvents) {
            $activityKey = $activityNumEvents['activity_id'];
            if (isset($activitiesMap[$activityKey])) {
                $activitiesMap[$activityKey]['num_events'] = $activityNumEvents['num_events'];
            }
        }
        return $activitiesMap;
    }
}