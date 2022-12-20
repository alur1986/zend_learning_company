<?php

namespace Resource\Doctrine\Event\Subscriber;


use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber as DoctrineSubscriberInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Subscriber implements
        DoctrineSubscriberInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents ()
    {
        return [
            Events::postLoad
        ];
    }

    /**
     * PostLoad event subscriber
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad (LifecycleEventArgs $args)
    {
    	$activity = $args->getObject();
        $entityManager = $args->getObjectManager();

        // do not proceed if the entity is not of Page instance
        if (!$activity instanceof Entity\LearningActivity) {
            return;
        }
        $activityId = $activity['activity_id'];
        $activityType = $activity['activity_type'];

        // only proceed if activity type is RESOURCE
        if ($activityType !== 'resource') {
            return;
        }

        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $serviceLocator = $this->getServiceLocator();
        $router = $serviceLocator->get('Router');

        $files = [];

        /* @var $fileManager \Savve\FileManager\FileManager */
        $fileManager = $serviceLocator->get('Savve\FileManager');

        // get module options/settings for file uploads
        /* @var $options \Learning\Service\OptionsService */
        $options = $serviceLocator->get('Learning\Options');

        // check the new file upload path
        $fileUploadPath = $options->getFileUploadPath();
        $fileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($fileUploadPath)) {
            Stdlib\ArrayUtils::arrayPush($files, $fileManager->readFiles($fileUploadPath));
        }

        // check the old file upload path
        $oldFileUploadPath = $options->getOldCourseFilePath();
        $oldFileUploadPath = $oldFileUploadPath . DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($oldFileUploadPath)) {
            Stdlib\ArrayUtils::arrayPush($files, $fileManager->readFiles($oldFileUploadPath));
        }

        // sort filename alphabetically
        usort($files, function  ($a, $b)
        {
            return strcasecmp($a['filename'], $b['filename']);
        });

        // only load if not previously loaded
        if ($activity['files'] === null) {
            $activity['files'] = $files;
        }
    }
}