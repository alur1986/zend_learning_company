<?php

namespace Resource\Service;

use Learning\Service\LearningService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;

class ResourceService extends AbstractService
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
            ->leftJoin('activity.resources', 'resources')
            ->select('activity, site, resources')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    public function findOneResourceById ($resourceId)
    {
    }

    /**
     * Find ONE resource by filename and activity ID
     *
     * @param string $filename
     * @param integer $activityId
     * @return Entity\ResourceFiles
     */
    public function findOneResourceByFilename ($filename, $activityId)
    {
        $repository = $this->resourceFileRepository();

        // create query
        $qb = $repository->createQueryBuilder('resource')
            ->select('resource')
            ->where('resource.filename = :filename AND resource.activity = :activityId')
            ->setParameter('filename', $filename)
            ->setParameter('activityId', $activityId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Creates an Internet Shortcut file
     *
     * @param string $destinationFileName
     */
    public function createInternetShortcutFile ($url, $destinationFileName)
    {
        try {
	        // create a URL internet location file
	        $iniConfig = new \Zend\Config\Config([], true);
	        $iniConfig->InternetShortcut = [];
	        $iniConfig->InternetShortcut->URL = $url;

	        // write to file
	        $writer = new \Zend\Config\Writer\Ini();
	        $writer->toFile($destinationFileName, $iniConfig);
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save the resource file data in repository
     *
     * @param array|\Traversable $data
     * @param integer $activityId
     * @return Entity\ResourceFiles
     */
    public function saveResourceFile ($data, $activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();
	        $repository = $this->resourceFileRepository();

	        // check if the file already exists in the repository
	        $filename = $data['filename'];
			$resource = $this->findOneResourceByFilename($filename, $activityId);
	        if (!$resource) {
	            // get the learning activity entity
	            $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);

	            // create the new resource entity
	            $data = Stdlib\ObjectUtils::extract($data);
	            $resource = new Entity\ResourceFiles();
	            $resource['activity'] = $activity;
	        }
	        $resource['filename'] = isset($data['filename']) ? $data['filename'] : null;
	        $resource['title'] = isset($data['title']) ? $data['title'] : $resource['filename'];
	        $resource['filetype'] = isset($data['filetype']) ? $data['filetype'] : null;

	        // save in repository
	        $entityManager->persist($resource);
	        $entityManager->flush($resource);
	        $entityManager->clear();

	        return $resource;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete the resource file data from repository
     *
     * @param string $filename
     */
    public function deleteResourceFile ($filename, $activityId)
    {
        try {
	        $entityManager = $this->getEntityManager();
	        $repository = $this->resourceFileRepository();

	        $query = $entityManager->createQueryBuilder()
	            ->delete('Savvecentral\Entity\ResourceFiles', 'resource')
	            ->where('resource.filename = :filename AND resource.activity = :activityId')
	            ->setParameter('filename', $filename)
	            ->setParameter('activityId', $activityId)
	            ->getQuery()
	            ->execute();
	        $entityManager->clear();

        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the resource file doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function resourceFileRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\ResourceFiles');
        return $repository;
    }
}