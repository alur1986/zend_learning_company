<?php

namespace Authentication\Factory\Service;

use Authentication\Service\Options as Service;
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
     * Get the ADFS Options config array
     * !! This can probaly be removed as soon as testing is completed !!
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
        if (!isset($config['ldap']) || !is_array($config['ldap'])) {
            return [];
        }
        $config = $config['ldap'];

        return $config;
    }
}