<?php

namespace Learner\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllLearnersFactory implements
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

        // get all active learners
        $learners = $service->findAllBySiteId($siteId);

        return $learners ?  : false;
    }
}