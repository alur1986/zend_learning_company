<?php

namespace Authorization\Guard\GuardManager;

use Authorization\Guard\GuardManager\GuardProviderInterface;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\ServiceManager\Plugin\AbstractPluginManager;
use Zend\Stdlib\InitializableInterface;

class GuardProviderPluginManager extends AbstractPluginManager
{

    /**
     * Validate the plugin
     *
     * Checks that the plugin loaded is either a valid callback or an instance of GuardProviderInterface.
     *
     * @param mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin ($plugin)
    {
        // hook to perform various initialization, when the element is not created through the factory
        if ($plugin instanceof InitializableInterface) {
            $plugin->init();
        }

        if ($plugin instanceof GuardProviderInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf('Plugin of type %1.s is invalid; must implement %2.s', (is_object($plugin) ? get_class($plugin) : gettype($plugin)), __NAMESPACE__ . '\GuardProviderInterface'));
    }
}