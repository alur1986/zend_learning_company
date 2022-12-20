<?php

namespace Learning\Factory\Service;

use Zend\Config\Config;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ActivityTypesServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array Collection of guard services
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator;

        $config = $this->getConfig($serviceLocator);
        $config = new Config($config);
        return $config;
    }

    /**
     * Get the module optional config array from the Configuration service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    public function getConfig (ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!$config) {
            return [];
        }

        if (!isset($config['learning_options']) || empty($config['learning_options'])) {
            return [];
        }

        $config = $config['learning_options'];
        if (!isset($config['activity_types']) || empty($config['activity_types'])) {
            return [];
        }

        $config = $config['activity_types'];
        return $config;
    }
}