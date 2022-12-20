<?php

namespace Learner\Factory\Service;

use Learner\Service\Options as Service;
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
        $options = new Service($config);
        return $options;
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
        if (!isset($config['learner_config']) || !is_array($config['learner_config'])) {
            return [];
        }

        $config = $config['learner_config'];
        return $config;
    }
}