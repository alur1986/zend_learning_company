<?php

namespace Notification\Factory\Service;

use Notification\Service\NotificationService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class NotificationServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $doctrineManager \Doctrine\ORM\EntityManager */
        $doctrineManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        // service instance
        $service = new Service($doctrineManager);
        return $service;
    }
}