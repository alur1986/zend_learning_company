<?php

namespace Learning\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearningTypeActivitiesServiceFactory implements
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
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');

        /* @var $service \Learning\Service\LearningService */
        $service = $serviceLocator->get('Learning\Service');
        $activities = $service->findAllLearningActivitiesBySiteId($siteId, 'learning');

        return $activities;
    }
}