<?php

namespace WrittenAssessment\Factory\Service;

use WrittenAssessment\Service\WrittenAssessmentService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class WrittenAssessmentServiceFactory implements
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