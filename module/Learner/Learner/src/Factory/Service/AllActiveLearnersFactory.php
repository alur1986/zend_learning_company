<?php

namespace Learner\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllActiveLearnersFactory implements
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

        /* @var $service \Learner\Service\LearnerService */
        $service = $serviceLocator->get('Learner\Service');
        $learners = $service->findAllActiveBySiteId($siteId);

        return $learners ? : false;
    }
}