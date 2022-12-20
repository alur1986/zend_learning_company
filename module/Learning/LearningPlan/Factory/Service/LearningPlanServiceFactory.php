<?php

namespace LearningPlan\Factory\Service;

use LearningPlan\Service\LearningPlan as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearningPlanServiceFactory implements
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

        /* @var $options \LearningPlan\Service\Options */
        $options = $serviceLocator->get('LearningPlan\Options');

        $service = new Service($entityManager, $options);

        return $service;
    }
}