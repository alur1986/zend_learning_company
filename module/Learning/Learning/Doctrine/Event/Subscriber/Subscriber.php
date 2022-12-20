<?php

namespace Learning\Doctrine\Event\Subscriber;

use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber as DoctrineSubscriberInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Subscriber implements
        DoctrineSubscriberInterface
{

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents ()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }


    /**
     * Events::prePersist event listener
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is skeleton
        if (!($entity instanceof Entity\LearningActivity)) {
            return;
        }

        // set the date created as current date time
        $entity['date_created'] = new \DateTime();
    }

    /**
     * Events::preUpdate event subscriber/listener
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate (PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is skeleton
        if (!($entity instanceof Entity\LearningActivity)) {
            return;
        }

        // set the date_updated as current date time
        $entity['date_updated'] = new \DateTime();
    }
}