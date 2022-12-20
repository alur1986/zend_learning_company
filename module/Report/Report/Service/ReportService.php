<?php

namespace Report\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;

class ReportService extends AbstractService
{

    /**
     * Find ALL report filters given site ID and report type
     *
     * @param integer $siteId
     * @param string $type
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllFiltersBySiteId ($siteId, $type)
    {
        $repository = $this->reportFilterRepository();

        // create query
        $qb = $repository->createQueryBuilder('filter')
            ->select('filter')
            ->leftJoin('filter.site', 'site')
            ->where('site.siteId = :siteId AND filter.type = :type')
            ->setParameter('siteId', $siteId)
            ->setParameter('type', $type)
            ->add('orderBy', 'filter.title ASC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Find ONE filter by filter ID
     *
     * @param integer $filterId
     * @return Entity\ReportFilter
     */
    public function findOneFilterById ($filterId)
    {
        $repository = $this->reportFilterRepository();

        // create query
        $qb = $repository->createQueryBuilder('filter')
            ->select('filter, site')
            ->leftJoin('filter.site', 'site')
            ->where('filter.filterId = :filterId')
            ->setParameter('filterId', $filterId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Delete ONE filte from repository
     *
     * @param integer $filterId
     * @throws Exception
     */
    public function deleteFilter ($filterId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $filter = $entityManager->getReference('Savvecentral\Entity\ReportFilter', $filterId);

            // remove filter from repository
            $entityManager->remove($filter);
            $entityManager->flush($filter);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the report template doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function reportTemplateRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\ReportTemplate');
        return $repository;
    }

    /**
     * Get the report filter doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function reportFilterRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\ReportFilter');
        return $repository;
    }
}