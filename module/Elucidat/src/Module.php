<?php

namespace Savvecentral\Elucidat;

use Zend\Stdlib;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleManager;

class Module
{

    /**
     * Initialize module
     *
     * @see \Zend\ModuleManager\Feature\InitProviderInterface::init()
     */
    public function init (ModuleManager $moduleManager)
    {
        /* @var $event \Zend\ModuleManager\ModuleEvent */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $serviceListener \Zend\ModuleManager\Listener\ServiceListener */

        $event = $moduleManager->getEvent();
        $eventManager = $moduleManager->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $serviceManager = $event->getParam('ServiceManager');
        $serviceListener = $serviceManager->get('ServiceListener');
    }

    /**
     * Listen to the bootstrap event
     *
     * @see \Zend\ModuleManager\Feature\BootstrapListenerInterface::onBootstrap()
     * @param EventInterface $event
     */
    public function onBootstrap (EventInterface $event)
    {
        /* @var $application \Zend\Mvc\Application */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManager */

        $application = $event->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $sharedEventManager = $eventManager->getSharedManager();
    }

    /**
     * Module configuration
     * Returns configuration to merge with application configuration
     *
     * @see \Zend\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     * @return array
     */
    public function getConfig ()
    {
        $pattern = __DIR__ . '/../config/{,*}.php';
        $config = [];
        foreach (glob($pattern, GLOB_NOSORT | GLOB_BRACE) as $filename) {
            $config = Stdlib\ArrayUtils::merge($config, include_once $filename);
        }

        return $config;
    }

    /**
     * Autoloader configuration
     *
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     * @return array
     */
    public function getAutoloaderConfig ()
    {
        $autoloader = [];

        // if autoload_config file exists
        if (realpath(__DIR__ . '/../autoload_config.php')) {
            $autoloader = Stdlib\ArrayUtils::merge($autoloader, include_once __DIR__ . '/../autoload_config.php');
        }

        // use the default
        elseif (realpath(__DIR__ . '/../autoload_classmap.php')) {
            $autoloader['Zend\Loader\ClassMapAutoloader'][] = realpath(__DIR__ . '/../autoload_classmap.php');
        }

        // if namespaces is not in the autoloader, add it
        if (!isset($autoloader['Zend\Loader\StandardAutoloader']['namespaces'])) {
            $autoloader['Zend\Loader\StandardAutoloader']['namespaces'] = [
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
            ];
        }

        return $autoloader;
    }
}