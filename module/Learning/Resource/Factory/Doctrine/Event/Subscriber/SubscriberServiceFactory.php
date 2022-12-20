<?php

namespace Resource\Factory\Doctrine\Event\Subscriber;

use Resource\Doctrine\Event\Subscriber\Subscriber as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class SubscriberServiceFactory implements
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
        $service = new Service($serviceLocator);
        return $service;
    }
}