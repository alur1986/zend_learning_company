<?php

namespace Scorm12\EventManager\Event\Listener;

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
        if (!($original['activity_type'] === $duplicate['activity_type'] && $original['activity_type'] === 'scorm12')) {
            return;
        }

        // duplicate the scorm12_activity data
        $scorm12Activity = $original['scorm12_activity'];
        if ($scorm12Activity) {
            $data = Stdlib\ObjectUtils::extract($scorm12Activity);

            // create new scorm12_activity data for the duplicate activity
            $newScorm12Activity = new Entity\Scorm12Activity();
            $newScorm12Activity = Stdlib\ObjectUtils::hydrate($data, $newScorm12Activity);
            $newScorm12Activity['activity'] = $duplicate;

            // save in repository
            $entityManager->persist($newScorm12Activity);
            $entityManager->flush($newScorm12Activity);
        }

        // duplicate the scorm12_items data
        $scorm12Items = $original['scorm12_items'];
        if ($scorm12Items) {
            foreach ($scorm12Items as $item) {
                $data = Stdlib\ObjectUtils::extract($item);

                // create new scorm12_item
                $newScorm12Item = new Entity\Scorm12Item();
                $newScorm12Item = Stdlib\ObjectUtils::hydrate($data, $newScorm12Item);
                $newScorm12Item['activity'] = $duplicate;

                // save in repository
                $entityManager->persist($newScorm12Item);
                $entityManager->flush($newScorm12Item);
            }
        }
    }
}
