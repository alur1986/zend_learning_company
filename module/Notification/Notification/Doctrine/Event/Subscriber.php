<?php

namespace Notification\Doctrine\Event;

use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
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
            Events::preUpdate,
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

        // only proceed if the entity is notification
        if (!($entity instanceof Entity\Notification)) {
            return;
        }

        $currentDateTime = new \DateTime();
        if (!isset($entity['active_from']) || empty($entity['active_from'])) {
            $entity['active_from'] = $currentDateTime;
        }

        if ((!isset($entity['status']) || empty($entity['status'])) && $entity['active_from'] <= $currentDateTime) {
            $entity['status'] = 'active';
        }

        if (!isset($entity['status']) || empty($entity['status'])) {
            $entity['status'] = 'new';
        }
    }

    /**
     * Events::preUpdate event subscriber/listener
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // only proceed if the entity is notification
        if (!($entity instanceof Entity\Notification)) {
            return;
        }

        $currentDateTime = new \DateTime();
        if (!isset($entity['active_from']) || empty($entity['active_from'])) {
            $entity['active_from'] = $currentDateTime;
        }

        if ((!isset($entity['status']) || empty($entity['status'])) && $entity['active_from'] <= $currentDateTime) {
            $entity['status'] = 'active';
        }
    }

    /**
     * PostLoad event subscriber
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad (LifecycleEventArgs $args)
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        // only proceed if the entity is notification
        if (!($entity instanceof Entity\Notification)) {
            return;
        }
        $currentDateTime = new \DateTime(date('Y-m-d h:i:s'));

        // activate some notifications with active from falling before and including current date
        if (isset($entity['active_from']) && !empty($entity['active_from']) && $entity['active_from'] <= $currentDateTime && in_array($entity['status'], [ 'new', 'active' ])) {
            $entity['status'] = 'active';
        }

        if ((!isset($entity['active_from']) || empty($entity['active_from']))) {
            $entity['status'] = 'active';
        }

        // expire some old notifications
        if (isset($entity['active_to']) && !empty($entity['active_to']) && $entity['active_to'] <= $currentDateTime) {
            $entity['status'] = 'expired';
        }

        // $meta = $entityManager->getClassMetadata(get_class($entity));
        // $unitOfWork->recomputeSingleEntityChangeSet($meta, $entity);
        // $entityManager->flush();
        // $entityManager->clear();
    }
}