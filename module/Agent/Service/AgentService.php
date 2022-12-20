<?php

namespace Agent\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Savvecentral\Entity;
use Savve\Doctrine\Service\AbstractService;

class AgentService extends AbstractService
{

    /**
     * Find one agent by agent ID
     *
     * @param integer $agentId
     * @return Entity\Agent
     */
    public function findOneByAgentId ($agentId)
    {
        $repository = $this->agentRepository();

        // create query
        $qb = $repository->createQueryBuilder('agent')
            ->leftJoin('agent.site', 'site')
            ->select('agent, site')
            ->where('agent.agentId = :agentId')
            ->setParameter('agentId', $agentId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find all agents in the repository
     *
     * @return ArrayCollection
     */
    public function findAll ()
    {
        $repository = $this->agentRepository();

        $status = ['active','inactive'];

        // create query
        $qb = $repository->createQueryBuilder('agent')
            ->leftJoin('agent.site', 'site')
            ->select('agent, site')
            ->where('agent.status IN (:status)')
            ->setParameter('status', $status)
            ->add('orderBy', 'agent.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find all agents within a site given the site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllBySiteId ($siteId)
    {
        $repository = $this->agentRepository();

        $status = ['active'];

        // create query
        $qb = $repository->createQueryBuilder('agent')
            ->leftJoin('agent.site', 'site')
            ->select('agent, site, site')
            ->where('site.siteId = :siteId')
            ->andWhere('agent.status IN (:status)')
            ->setParameter('siteId', $siteId)
            ->setParameter('status', $status)
            ->add('orderBy', 'agent.name ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find one agent by site, name and code
     *
     * @param integer $siteId
     * @param string  $code
     * @param string  $password
     * @return Entity\Agent
     */
    public function findOneByAgentSite ($siteId, $code, $password)
    {
        $repository = $this->agentRepository();

        // create query
        $qb = $repository->createQueryBuilder('agent')
            ->leftJoin('agent.site', 'site')
            ->select('agent, site')
            ->where('agent.code = :code')
            ->andWhere('agent.password = :password')
            ->andWhere('agent.status = :status')
            ->andWhere('site.siteId = :siteId')
            ->setParameter('code', $code)
            ->setParameter('password', $password)
            ->setParameter('status', 'active')
            ->setParameter('siteId', $siteId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find one agent code by site and password
     *
     * @param integer $siteId
     * @param string  $password
     * @return Entity\Agent
     */
    public function findOneAgentNameBySite ($siteId, $code)
    {
        $repository = $this->agentRepository();

        // create query
        $qb = $repository->createQueryBuilder('agent')
            ->leftJoin('agent.site', 'site')
            ->select('agent, site')
            ->where('agent.code = :code')
            ->andWhere('agent.status = :status')
            ->andWhere('site.siteId = :siteId')
            ->setParameter('code', $code)
            ->setParameter('status', 'active')
            ->setParameter('siteId', $siteId);

        // execute query
        $result = $repository->fetchOne($qb);
        return $result['name'];
    }

    /**
     * Create ONE new agent in the repository
     *
     * @param $entity Entity\Agent
     * @throws \Exception
     */
    public function create ($entity)
    {
        try {
            $entityManager = $this->getEntityManager();

            // associate the current platform
            $routeMatch = $this->getRouteMatch();
            $siteId     = $routeMatch->getParam('site_id');
            $site       = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
            $entity['site']   = $site;
            $entity['status'] = 'active';

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
     * Update ONE agent in the repository
     *
     * @param $entity Entity\Agent
     * @throws \Exception
     */
    public function update ($entity)
    {
        try {
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
     * Delete ONE agent from the repository
     *
     * @param $entity Entity\Agent
     * @throws \Exception
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
     * Get the agent repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function agentRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Agent');

        return $repository;
    }
}
