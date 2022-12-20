<?php

namespace Company\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Savvecentral\Entity;
use Savve\Doctrine\Service\AbstractService;

class CompanyService extends AbstractService
{

    /**
     * Find one company by company ID
     *
     * @param integer $companyId
     * @return Entity\Company
     */
    public function findOneByCompanyId ($companyId)
    {
        $repository = $this->companyRepository();

        // create query
        $qb = $repository->createQueryBuilder('company')
            ->leftJoin('company.platform', 'platform')
            ->select('company, platform')
            ->where('company.companyId = :companyId')
            ->setParameter('companyId', $companyId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find all companies in the repository
     *
     * @return ArrayCollection
     */
    public function findAll ()
    {
        $repository = $this->companyRepository();

        // create query
        $qb = $repository->createQueryBuilder('company')
            ->leftJoin('company.platform', 'platform')
            ->select('company, platform')
            ->add('orderBy', 'company.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find all companies within a platform given the platform ID
     *
     * @param integer $platformId
     * @return ArrayCollection
     */
    public function findAllByPlatformId ($platformId)
    {
        $repository = $this->companyRepository();

        // create query
        $qb = $repository->createQueryBuilder('company')
            ->leftJoin('company.platform', 'platform')
            ->leftJoin('company.site', 'site')
            ->select('company, platform, site')
            ->where('platform.platformId = :platformId')
            ->setParameter('platformId', $platformId)
            ->add('orderBy', 'company.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find all companies within a platform given the platform ID
     *
     * @param integer $platformId
     * @return ArrayCollection
     */
    public function findAllDataByPlatformId ($platformId)
    {
        $repository = $this->companyRepository();

        // create query
        $qb = $repository->createQueryBuilder('company')
        ->leftJoin('company.platform', 'platform')
        ->leftJoin('company.site', 'site')
        ->select('company, platform, site')
        ->where('platform.platformId = :platformId')
        ->setParameter('platformId', $platformId)
        ->add('orderBy', 'company.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        // get the counts as Dr Doctrine seems to have difficulty handling this
        $entityManager = $this->getEntityManager();
        $arr = array();
        foreach ($results as $key => $result) {
            $siteId = $result['site']['site_id'];
            // get learner count
            $dql = "SELECT COUNT(learner.userId)
                    FROM Savvecentral\\Entity\\Learner learner
                    WHERE learner.site = :siteId";
                $learnerCount = $entityManager->createQuery($dql)
                    ->setParameter('siteId',$siteId)
                    ->getOneOrNullResult();
            // save count
            $result['site']['num_learners'] = $learnerCount[1];

            // get groups count
            $dql = "SELECT COUNT(groups.groupId)
                    FROM Savvecentral\\Entity\\Groups groups
                    WHERE groups.site = :siteId";
            $groupCount = $entityManager->createQuery($dql)
            ->setParameter('siteId',$siteId)
            ->getOneOrNullResult();
            // save count
            $result['site']['num_groups'] = $groupCount[1];

            // get groups count
            $status = array('deleted');
            $dql = "SELECT COUNT(activitys.activityId)
                    FROM Savvecentral\\Entity\\LearningActivity activitys
                    WHERE activitys.site = :siteId AND activitys.status NOT IN (:status)";
            $activityCount = $entityManager->createQuery($dql)
            ->setParameter('siteId',$siteId)
            ->setParameter('status',$status)
            ->getOneOrNullResult();
            // save count
            $result['site']['num_activities'] = $activityCount[1];

            // save into new array
            $arr[$key] = $result;
        }
        return $arr; // $results;
    }

    /**
     * Create ONE new company in the repository
     *
     * @param Entity\Company $entity
     */
    public function create ($entity)
    {
        try {
            $entityManager = $this->getEntityManager();

            // associate the current platform
            $routeMatch = $this->getRouteMatch();
            $platformId = $routeMatch->getParam('platform_id');
            $platform = $entityManager->getReference('Savvecentral\Entity\Platform', $platformId);
            $entity['platform'] = $platform;

            // save
            $entityManager->persist($entity);
            $entityManager->flush($entity);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update ONE company in the repository
     *
     * @param Entity\Company $entity
     */
    public function update ($entity)
    {
        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($entity);
            $entityManager->flush($e);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE company from the repository
     *
     * @param Entity\Company $entity
     */
    public function delete ($entity)
    {
        try {
            $entity['status'] = 'deleted';
            $entityManager = $this->getEntityManager();
            $entityManager->persist($entity);
            $entityManager->flush($entity);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the company repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function companyRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Company');

        return $repository;
    }
}