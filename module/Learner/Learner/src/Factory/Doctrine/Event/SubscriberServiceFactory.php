<?php

namespace Learner\Factory\Doctrine\Event;

use Learner\Doctrine\Event\Subscriber as Service;
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
        $options = $serviceLocator->get('Learner\Options');
        $service = new Service($options);
        return $service;
    }
}