<?php

namespace Learning\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearningTypesServiceFactory implements
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
        $activityTypes = $serviceLocator->get('Learning\ActivityTypes');
        $config = $this->getConfig($serviceLocator);

        $collection = [];
        foreach ($config as $key) {
            $activityType = $activityTypes[$key];
            $collection[$key] = $activityType;
        }

        return $collection;
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
        if (!isset($config['learning_types']) || empty($config['learning_types'])) {
            return [];
        }

        $config = $config['learning_types'];
        return $config;
    }
}