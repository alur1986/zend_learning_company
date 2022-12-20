<?php

namespace Tincan\EventManager\Event\Listener;

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

        // only duplicate scorm12 learning activity
        if (!($original['activity_type'] === $duplicate['activity_type'] && $original['activity_type'] === 'tincan')) {
            return;
        }

        // duplicate the scorm12_activity data
        $tincanActivity = $original['scorm12_activity'];
        if ($tincanActivity) {
            $data = Stdlib\ObjectUtils::extract($tincanActivity);

            // create new scorm12_activity data for the duplicate activity
            $newTincanActivity = new Entity\Scorm12Activity();
            $newTincanActivity = Stdlib\ObjectUtils::hydrate($data, $newTincanActivity);
            $newTincanActivity['activity'] = $duplicate;

            // save in repository
            $entityManager->persist($newTincanActivity);
            $entityManager->flush($newTincanActivity);
        }

        // duplicate the tincan_items data
        $scorm12Items = $original['scorm12_items'];
        if ($scorm12Items) {
            foreach ($scorm12Items as $item) {
                $data = Stdlib\ObjectUtils::extract($item);

                // create new scorm12_item
                $newTincanItem = new Entity\Scorm12Item();
                $newTincanItem = Stdlib\ObjectUtils::hydrate($data, $newTincanItem);
                $newTincanItem['activity'] = $duplicate;

                // save in repository
                $entityManager->persist($newTincanItem);
                $entityManager->flush($newTincanItem);
            }
        }
    }
}
