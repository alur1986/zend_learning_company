<?php

namespace Resource\EventManager\Event\Listener;

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
        if (!($original['activity_type'] === $duplicate['activity_type'] && $original['activity_type'] === 'resource')) {
            return;
        }

        // duplicate the files
        $files = $original['resources'];
        if (!count($files)) {
            return;
        }
        foreach ($files as $file) {
            $data = Stdlib\ObjectUtils::extract($file);

            // create new ResourceFiles
            $newFile = new Entity\ResourceFiles();
            $newFile = Stdlib\ObjectUtils::hydrate($data, $newFile);


            $newFile['activity'] = $duplicate;

            // persist
            $entityManager->persist($newFile);
        }

        // save
        $entityManager->flush($newFile);
        $entityManager->clear();
    }
}