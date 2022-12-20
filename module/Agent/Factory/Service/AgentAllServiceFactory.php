<?php

namespace Agent\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AgentAllServiceFactory implements
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
        $siteId = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch()
            ->getParam('site_id');


        /* @var $service \Agent\Service\AgentService */
        $service = $serviceLocator->get('Agent\Service');
        $agents = $service->findAllBySiteId($siteId);

        return $agents;
    }
}