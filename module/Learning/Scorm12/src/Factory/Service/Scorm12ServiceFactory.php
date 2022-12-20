<?php

namespace Scorm12\Factory\Service;

use Scorm12\Service\Scorm12Service as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class Scorm12ServiceFactory implements
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
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $service = new Service($entityManager);
        return $service;
    }
}