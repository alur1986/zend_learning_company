<?php

namespace OnTheJobAssessment\Factory\Service;

use OnTheJobAssessment\Service\QuestionService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class QuestionServiceFactory implements
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