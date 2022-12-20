<?php

namespace Learning\EventManager\Listener;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;

class DuplicateActivityListener
{

    /**
     * Invoke the event listener
     *
     * @param EventInterface $event
     */
    public function __invoke (EventInterface $event)
    {
        /* @var $service \Learning\Service\LearningService */
        $service = $event->getTarget();

        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $service->getServiceLocator();

        /* @var $entityManager \Savve\Doctrine\ORM\EntityManager */
        $entityManager = $service->getEntityManager();

        // get the original learning activity
        $original = $event->getParam('original');

        // get the duplicate learning activity
        $duplicate = $event->getParam('duplicate');

        // only duplicate RESOURCE learning activity
        if (!($original['activity_type'] === $duplicate['activity_type'])) {
            return;
        }

        // copy the files from the original file path to the new duplicate resource file path

        /* @var $options \Learning\Service\OptionsService */
        $options = $serviceManager->get('Learning\Options');
        $fileUploadPath = $options->getFileUploadPath();

        // get the original file path
        $originalFileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $original['activity_id'];

        // define the duplicate file path
        $duplicateFileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $duplicate['activity_id'];

        /* @var $fileManager \Savve\FileManager\FileManager */
        $fileManager = $serviceManager->get('Savve\FileManager');

        // copy files
        if (file_exists($originalFileUploadPath)) {
            $success = $fileManager->copy($originalFileUploadPath, $duplicateFileUploadPath);
        }
    }
}