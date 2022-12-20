<?php

namespace Authorization\Guard\GuardManager;

use Authorization\Guard\GuardManager\GuardProviderPluginManager;
use Savve\Stdlib\Exception;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GuardProviderPluginManagerFactory implements
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
            throw new Exception\InvalidArgumentException('GuardProviderPluginManager requires that guard_manager config is provided', null, null);
        }
        $config = new Config($config);
        $pluginManager = new GuardProviderPluginManager($config);
        $pluginManager->setServiceLocator($serviceLocator);

        return $pluginManager;
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
        if (!isset($config['authorization']) || !is_array($config['authorization'])) {
            return [];
        }

        $config = $config['authorization'];
        if (!isset($config['guard_manager']) || !is_array($config['guard_manager'])) {
            return [];
        }

        $config = $config['guard_manager'];

        return $config;
    }
}