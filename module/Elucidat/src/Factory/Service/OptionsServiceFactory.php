<?php

namespace Elucidat\Elucidat\Factory\Service;

use Elucidat\Elucidat\Service\Options as Service;
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
     * @return multitype: unknown
     */
    protected function getConfig (ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('Config')) {
            return [];
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config['elucidat']) || !is_array($config['elucidat'])) {
            return [];
        }

        $config = $config['elucidat'];
        return $config;
    }
}