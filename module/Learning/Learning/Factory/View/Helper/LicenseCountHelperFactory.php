<?php

namespace Learning\Factory\View\Helper;

use Learning\View\Helper\LicenseCount as Helper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LicenseCountHelperFactory implements
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
        $helper = new Helper($serviceManager);
        return $helper;
    }
}