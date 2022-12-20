<?php

namespace LearningPlan\Factory\Service;

use LearningPlan\Service\Options as Service;
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
        if (!$serviceLocator->has('Config')) {
            return [];
        }

        $config = $serviceLocator->get('Config');
        if (!isset($config['learning_plan']) || empty($config['learning_plan'])) {
            return [];
        }

        $config = $config['learning_plan'];
        return $config;
    }
}