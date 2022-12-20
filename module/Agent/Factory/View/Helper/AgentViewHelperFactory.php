<?php

namespace Agent\Factory\View\Helper;

use Agent\View\Helper\GetAgencyName as Helper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AgentViewHelperFactory implements
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
        $serviceManager = $serviceLocator->getServiceLocator();

        /* @var $service \Agent\Service\AgentService */
        $service = $serviceManager->get('Agent\Service');

        $helper = new Helper( $service );
        return $helper;
    }
}