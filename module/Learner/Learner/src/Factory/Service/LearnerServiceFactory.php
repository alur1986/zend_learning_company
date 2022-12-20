<?php

namespace Learner\Factory\Service;

use Learner\Service\LearnerService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnerServiceFactory implements
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
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $options = $serviceLocator->get('Learner\Options');
        $service = new Service($entityManager, $options);
        return $service;
    }
}