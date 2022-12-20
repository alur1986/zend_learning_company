<?php

namespace Authorization\Factory\Guard;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GuardsServiceProviderFactory implements
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
        /* @var $pluginManager \Authorization\Guard\GuardManager\GuardProviderPluginManager */
        $pluginManager = $serviceLocator->get('Authorization\Guard\GuardProviderPluginManager');

        $guards = [];
        foreach ($pluginManager->getCanonicalNames() as $name => $item) {
            $guards[] = $pluginManager->get($name);
        }

        return $guards;
    }
}