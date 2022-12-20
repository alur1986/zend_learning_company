<?php

namespace Authorization\Factory\Service;

use Authorization\Service\Options as Service;
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
            return false;
        }

        $service = new Service($config);

        return $service;
    }

    /**
     * Get config array
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    protected function getConfig (ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')) {
            return [];
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config['authorization']) || !is_array($config['authorization'])) {
            return [];
        }

        $config = $config['authorization'];
        if (!isset($config['options']) || !is_array($config['options'])) {
            return [];
        }

        $config = $config['options'];
        return $config;
    }
}