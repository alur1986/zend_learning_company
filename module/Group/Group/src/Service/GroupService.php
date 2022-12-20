<?php

namespace Group\Service;

use Group\Event\Event;
use Savvecentral\Entity;
use Savve\Doctrine\Service\AbstractService;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class GroupService extends AbstractService {

    /**
     * Find ONE group by group ID
     *
     * @param integer $groupId
     *
     * @return Entity\Groups
     */
    public function findOneByGroupId ($groupId)
    {
        $learnerStatus = ['new', 'active', 'inactive'];
        $repository = $this->groupRepository ();

        // create query
        $qb = $repository->createQueryBuilder ('groups')
                 ->leftJoin ('groups.site', 'site')
                 ->leftJoin ('groups.groupLearners', 'groupLearners')
                 ->leftJoin ('groupLearners.learner', 'learner', 'WITH', 'learner.status IN (:learnerStatus)')
                 ->leftJoin ('learner.employment', 'employment')
                 ->leftJoin ('learner.settings', 'settings')
                 ->select ('groups, site, groupLearners, learner, employment, settings')
                 ->where ('groups.groupId = :groupId')
                 ->setParameter ('groupId', $groupId)
                 ->setParameter ('learnerStatus', $learnerStatus)
                 ->add ('orderBy', 'groups.name ASC, learner.firstName ASC');

        // execute query
        $result = $repository->fetchOne ($qb);

        return $result;
    }

    /**
     * Find ONE group by group ID
     *
     * @param integer $groupId
     *
     * @return Entity\Groups
     */
    public function findOneGroupDetailsByGroupId ($groupId, $userId = false)
    {
        $learnerStatus = ['new', 'active', 'inactive'];
        $repository = $this->groupRepository ();

        /* try this alternative - same as the findAll function  - get the correct admin count for 1 group only */
        $dql = "SELECT groups
                FROM Savvecentral\\Entity\\Groups groups
                WHERE groups.groupId = :groupId
                ORDER BY groups.name ASC";

        $params['groupId'] = $groupId;
        $groups = $repository->fetchCollection($dql, $params);

        $params = false; // reset this or else Doctrine complains
        $arr = array();
        /* get the Learners via groupLearners */
        foreach ($groups as $group) {
            $groupId = $group['group_id'];
            $dql = "SELECT groupLearners, learner, COUNT(learner) AS number_of_learners
                    FROM Savvecentral\\Entity\\GroupLearners groupLearners
                    LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                    WHERE groupLearners.group = :groupId
                    ORDER BY learner.firstName ASC, learner.lastName ASC";
            $params['groupId'] = $groupId;
            $results = $repository->fetchCollection($dql, $params);

            $numLearners = $results[0]['number_of_learners'];
            $arr[] = array("group_id"=>$groupId, "name"=>$group['name'], "number_of_learners"=>$numLearners, "group"=>$group, "group_learners"=>$results[0][0]);
        }
        $results = $arr;

        $params = false;
        $arr = array();
        /* get the admin count */
        foreach($results as $key => $result) {
            $groupId = $result['group_id'];
            $dql = "SELECT groupLearners
                FROM Savvecentral\\Entity\\GroupLearners groupLearners
                LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                WHERE groupLearners.group = :groupId AND groupLearners.role = :learnerRole  AND learner.status NOT IN ('deleted','expired')";


            $params['groupId'] = $groupId;
            $params['learnerRole'] = 'admin';
            // get the admin result
            $results = $repository->fetchCollection($dql, $params);

            $result['number_of_admins'] = count($results); // number of admins in the group
            $group                = $result['group'];      // get the 'group' object
            $isAdministered       = $group->isAdministeredBy($userId); // check here for the 'has admin' priviledge
            $result['admins']     = $results;
            $result['isAdmin']     = $isAdministered;
            // rebuild the result
            $arr[$key] = $result;
        }

        return $arr[0];
    }

    /**
     * Find all groups by site Id
     * Used in reports and other places
     * @deprecated  Please use findAllGroupDetailsBySiteId instead to get correct numbers
     */
    public function findAllGroupsBySiteId ($siteId)
    {
        $learnerStatus = [ 'new', 'active', 'inactive' ];
        $repository = $this->groupRepository();

        // create query
        $dql = "SELECT groups
                FROM Savvecentral\Entity\Groups groups
                LEFT JOIN groups.site site
                LEFT JOIN groups.groupLearners groupLearners
                LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                WHERE site.siteId = :siteId
                GROUP BY groups.groupId, learner.userId
                ORDER BY groups.name ASC, learner.firstName ASC, learner.lastName ASC";
        $params['siteId'] = $siteId;

        // execute query
        $results = $repository->fetchCollection($dql, $params);

        return $results;
    }

    /**
     * Find ALL groups by site ID
     * Used in the group directory to get more information on the active learners / admins
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllGroupDetailsBySiteId ($siteId, $userId)
    {
        $learnerStatus = [ 'new', 'active', 'inactive' ];
        /** @var \Savve\Doctrine\Repository\EntityRepository $repository */
        $repository = $this->groupRepository();

        // create query //		GROUP BY groups.groupId  //  GROUP BY groupLearners.group
     /*   $dql = "SELECT groups, groupLearners, learner, COUNT(groupLearners.learner) AS number_of_learners
                FROM Savvecentral\\Entity\\Groups groups
                LEFT JOIN groups.site site
                LEFT JOIN groups.groupLearners groupLearners
                LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                WHERE site.siteId = :siteId
                GROUP BY groupLearners.group
                ORDER BY groups.name ASC, learner.firstName ASC, learner.lastName ASC";

        $params['siteId'] = $siteId;
        $results = $repository->fetchCollection($dql, $params); */

        /*
         * So that we can get the 'Empty' groups as well - get all the groups first
         */
        $dql = "SELECT groups
                FROM Savvecentral\\Entity\\Groups groups
                WHERE groups.site = :siteId
                ORDER BY groups.name ASC";

        $params['siteId'] = $siteId;
        $groups = $repository->fetchCollection($dql, $params);

        $params = false; // reset this or else Doctrine complains
        $arr = array();
        /* iterate through the found groups and get the Learners vie groupLearners */
        foreach ($groups as $group) {
            $groupId = $group['group_id'];
            $dql = "SELECT groupLearners, learner, COUNT(learner) AS number_of_learners
                    FROM Savvecentral\\Entity\\GroupLearners groupLearners
                    LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                    WHERE groupLearners.group = :groupId
                    ORDER BY learner.firstName ASC, learner.lastName ASC";
            $params['groupId'] = $groupId;

            $results = $repository->fetchCollection($dql, $params);


            $numLearners = $results[0]['number_of_learners'];
            $group = array("group_id"=>$groupId, "name"=>$group['name'], "number_of_learners"=>$numLearners, "group"=>$group, "group_learners"=>$results[0][0]);
            $arr[] = $group;
        }
        $results = $arr;

        $params = false;
        $arr = array();

        /* TODO: !! until I can figure out how to include this count in the original query above !!
         * /// COUNT(groupLearners.group) AS number_of_admins
         * Iterate the groups and get the 'Admins' and count them for each group
         */
        foreach($results as $key => $result) {
            $groupId = $result['group_id'];
            $dql = "SELECT groupLearners
                FROM Savvecentral\\Entity\\GroupLearners groupLearners
                LEFT JOIN groupLearners.learner learner WITH learner.status NOT IN ('deleted', 'expired')
                WHERE groupLearners.group = :groupId AND groupLearners.role = :learnerRole  AND learner.status NOT IN ('deleted','expired')";


            $params['groupId'] = $groupId;
            $params['learnerRole'] = 'admin';
            // get the admin result
            $results = $repository->fetchCollection($dql, $params);

            $result['number_of_admins'] = count($results); // number of admins in the group
            $group                = $result['group'];      // get the 'group' object
            $isAdministered       = $group->isAdministeredBy($userId); // check here for the 'has admin' priviledge
            $result['admins']     = $results;
            $result['isAdmin']    = $isAdministered;
            // rebuild the result
            $arr[$key] = $result;
        }
        // return the array
        return $arr;
    }

    /**
     * Finds ALL ACTIVE groups by site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllActiveGroupsBySiteId ($siteId)
    {
        $status = [ 'new', 'active' ];

        // find all groups by site ID
        $groups = $this->findAllGroupsBySiteId($siteId);

        // filter by status
        $expr = Criteria::expr();
        $criteria = new Criteria();
        $criteria->where($expr->in('status', $status));

        // execute filter
        $groups = $groups->matching($criteria);

        return $groups;
    }

    /**
     * Finds ALL INACTIVE groups by site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllInactiveGroupsBySiteId ($siteId)
    {
        // find ALL groups by site ID
        $groups = $this->findAllGroupsBySiteId($siteId);

        // filter by inactive status
        $expr = Criteria::expr();
        $criteria = new Criteria();
        $criteria->where($expr->eq('status', 'inactive'));

        $groups = $groups->matching($criteria);

        return $groups;
    }

    /**
     * Find ALL groups a learner is a member of by given learner (user) ID
     *
     * @param integer $learnerId
     * @return ArrayCollection
     */
    public function findAllGroupsByLearner ($learnerId)
    {
        $repository = $this->groupRepository();

        // create query
        $qb = $repository->createQueryBuilder('groups')
            ->leftJoin('groups.site', 'site')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.learner', 'learner')
            ->select('groups, site, groupLearners, learner')
            ->where('learner.userId = :learnerId')
            ->setParameter('learnerId', $learnerId);

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find all groups managed by the admin
     *
     * @param integer $learnerId
     * @return ArrayCollection
     */
    public function findAllGroupsByAdmin ($learnerId)
    {
        $repository = $this->groupRepository();

        // create query
        $qb = $repository->createQueryBuilder('groups')
            ->leftJoin('groups.site', 'site')
            ->leftJoin('groups.groupLearners', 'groupLearners')
            ->leftJoin('groupLearners.learner', 'learner')
            ->select('groups, site, groupLearners, learner')
            ->where('learner.userId = :learnerId AND groupLearners.role = :groupRole')
            ->setParameter('learnerId', $learnerId)
            ->setParameter('groupRole', 'admin');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ALL learners in the group
     *
     * @param array|\Traversable $groupIds
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllLearnersByGroups ($groupIds)
    {
        if (!is_array($groupIds)) {
            $groupIds = (array) $groupIds;
        }

        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');

        // create query
        $params = [];
        $dql = "SELECT learner
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                WHERE groups.groupId IN (:groupId)
                ORDER BY learner.firstName, learner.lastName";
        $params['groupId'] = $groupIds;

        // execute query
        $results = $entityManager->createQuery($dql)
            ->useResultCache(true, (60 * 60 * 24), Stdlib\StringUtils::dashed($dql . serialize($params)))
            ->setParameters($params)
            ->getResult();
        $results = new ArrayCollection($results);

        return $results;
    }

    /**
     * Find ALL learners in the groups by status
     *
     * @param array|\Traversable $groupIds
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllLearnersByGroupsAndStatus ($groupIds, $status)
    {
        if (!is_array($groupIds)) {
            $groupIds = (array) $groupIds;
        }

        if (!is_array($status)) {
            $status = (array) $status;
        }

        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');

        // create query
        $params = [];
        $dql = "SELECT learner
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups
                WHERE groups.groupId IN (:groupId) AND learner.status IN (:status)
                ORDER BY learner.firstName, learner.lastName";

        $params['groupId'] = $groupIds;

        $params['status']  = $status;

        // execute query
        $results = $entityManager->createQuery($dql)
            ->useResultCache(true, (60 * 60 * 24), Stdlib\StringUtils::dashed($dql . serialize($params)))
            ->setParameters($params)
            ->getResult();
        $results = new ArrayCollection($results);

        return $results;
    }

    /**
     * Create ONE group
     *
     * @param $data
     * @param $siteId
     * @return Stdlib\stdClass|Entity\Groups
     * @throws \Exception
     */
    public function createGroup ($data, $siteId)
    {
        try {
	        $data = Stdlib\ObjectUtils::extract($data);

	        // site associated entity
	        $entityManager = $this->getEntityManager();
	        $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

	        // create group entity
	        $group = new Entity\Groups();
	        $group = Stdlib\ObjectUtils::hydrate($data, $group);
	        $group['site'] = $site;
	        $group['status'] = 'active';

	        // save in repository
	        $entityManager->persist($group);
	        $entityManager->flush($group);
	        $entityManager->clear();

	        return $group;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE group
     *
     * @param Entity\Groups $group
     * @return Entity\Groups
     * @throws \Exception
     */
    public function updateGroup (Entity\Groups $group)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // save in repository
	        $entityManager->persist($group);
	        $entityManager->flush($group);
	        $entityManager->clear();

	        return $group;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE group
     *
     * @param Entity\Groups $entity
     * @throws \Exception
     */
    public function deleteGroup (Entity\Groups $group)
    {
        try {
	        $entityManager = $this->getEntityManager();

	        // remove from repository
	        $entityManager->remove($group);
	        $entityManager->flush($group);
	        $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the group repository
     *
     * @return \Group\Doctrine\Repository\Group
     */
    public function groupRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Groups');
    }
}