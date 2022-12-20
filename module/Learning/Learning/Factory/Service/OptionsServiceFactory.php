<?php

namespace Learning\Factory\Service;

use Learning\Service\OptionsService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OptionsServiceFactory implements
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
        $config = $this->getConfig($serviceLocator);
        if (!$config) {
            return new Service();
        }

        return new Service($config);
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
        return $config;
    }
}