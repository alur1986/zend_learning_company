<?php

namespace Learning\Factory\Service;

use Doctrine\Common\Collections\Criteria;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ActiveLearningActivitiesServiceFactory implements
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
        $activities = $service->findAllLearningActivitiesBySiteId($siteId);

        $expr = Criteria::expr();
        $criteria = new Criteria();
        $criteria->where($expr->in('status', ['new','active']));
        $activities = $activities->matching($criteria);

        return $activities;
    }
}