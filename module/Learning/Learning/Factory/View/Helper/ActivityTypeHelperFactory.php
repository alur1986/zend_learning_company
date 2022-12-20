<?php

namespace Learning\Factory\View\Helper;

use Learning\View\Helper\ActivityType as Helper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ActivityTypeHelperFactory implements
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
        $activityTypes = $serviceManager->get('Learning\ActivityTypes');
        $helper = new Helper($activityTypes);
        return $helper;
    }
}