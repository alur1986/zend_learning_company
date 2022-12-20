<?php

namespace Company\Doctrine\Event;

use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
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
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::onFlush,
            Events::postLoad
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

        // only proceed if the entity is Company
        if (!($entity instanceof Entity\Company)) {
            return;
        }
    }

    /**
     * Events::postPersist event subscriber/listener
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is Company
        if (!($entity instanceof Entity\Company)) {
            return;
        }
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

        // only proceed if the entity is Company
        if (!($entity instanceof Entity\Company)) {
            return;
        }
    }

    /**
     * Events::postUpdate event subscriber/listener
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is Company
        if (!($entity instanceof Entity\Company)) {
            return;
        }
    }

    /**
     * Events::onFlush event subscriber/listener
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush (OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            // do something with new entities
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            // do something with updated entities
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            // do something with deleted entities
        }

        foreach ($unitOfWork->getScheduledCollectionDeletions() as $col) {
            // do something
        }

        foreach ($unitOfWork->getScheduledCollectionUpdates() as $col) {
            // do something
        }
    }

    /**
     * PostLoad event subscriber
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is Company
        if (!($entity instanceof Entity\Company)) {
            return;
        }
    }
}