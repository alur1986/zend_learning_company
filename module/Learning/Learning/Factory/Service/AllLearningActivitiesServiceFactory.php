<?php

namespace Learning\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllLearningActivitiesServiceFactory implements
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
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return false;
        }
        $siteId = $routeMatch->getParam('site_id');

        /* @var $service \Learning\Service\LearningService */
        $service = $serviceLocator->get('Learning\Service');
        $activities = $service->findAllLearningActivitiesBySiteId($siteId);

        return $activities;
    }
}