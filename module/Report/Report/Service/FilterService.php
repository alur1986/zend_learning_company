<?php

namespace Report\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class FilterService extends AbstractService
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
     * Save the report filter
     *
     * @param array|\Traversable $data
     * @return Entity\ReportFilter
     * @throws \Exception
     */
    public function createFilter ($data, $siteId, $type)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            $entityManager = $this->getEntityManager();
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // create new entity
            $filter = new Entity\ReportFilter();
            $filter = Stdlib\ObjectUtils::hydrate($data, $filter);
            $filter['site'] = $site;
            $filter['type'] = $type;

            // save in repository
            $entityManager->persist($filter);
            $entityManager->flush($filter);
            $entityManager->clear();

            return $filter;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save the report filter
     *
     * @param array $data
     * @return Entity\ReportFilter
     * @throws \Exception
     */
    public function updateFilter ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            if (!isset($data['filter_id']) || empty($data['filter_id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot update the filter. The filter ID is required'));
            }

            $entityManager = $this->getEntityManager();

            // find the filter
            $filterId = $data['filter_id'];
            $entity = $this->findOneFilterById($filterId);
            if (!$entity) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot update the filter. The filter does not exists.'));
            }
            $filterData = json_decode($entity['filter'], true);

            // merge with the filter data
            if (isset($data['filter'])) {
                if (is_string($data['filter']) && @json_decode($data['filter']) && (json_last_error() == JSON_ERROR_NONE)) {
                    $data['filter'] = json_decode($data['filter'], true);
                }
                $filterData = array_merge($filterData, $data['filter']);
            }

            $entity['filter'] = json_encode($filterData);

            unset($data['filter_id']);
            unset($data['filter']);

            $entity = Stdlib\ObjectUtils::hydrate($data, $entity);

            // save in repository
            $entityManager->persist($entity);
            $entityManager->flush($entity);
            $entityManager->clear();

            return $entity;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE filter from repository
     *
     * @param integer $filterId
     * @throws \Exception
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