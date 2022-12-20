<?php

namespace Report;

use Savve\Mvc\AbstractModule;
use ReflectionClass;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\ModuleRouteListener;

class Module extends AbstractModule
{

//     public function init (ModuleManager $moduleManager)
//     {
//         $eventManager = $moduleManager->getEventManager();
//         $eventManager->attach(ModuleEvent::EVENT_LOAD_MODULE, array($this, 'mergeAdapterConfig'), -100);
//     }

//     public function mergeAdapterConfig (ModuleEvent $event)
//     {
//         /* @var $configListener \Zend\ModuleManager\Listener\ConfigListener */

//         $configListener = $event->getConfigListener();
//         $config = $configListener->getMergedConfig(false);
//         if (isset($config['report_config']) && isset($config['report_config']['adapters'])) {
//             $adapters = $config['report_config']['adapters'];
//             foreach ($adapters as $adapter) {
//                 $adapter .= '\\Adapter';

//                 // get the adapter config file if it exists
//                 if (class_exists($adapter, true)) {

//                     $reflection = new ReflectionClass($adapter);
//                     if ($reflection->hasMethod('getBlockConfig')){
//                         $blockConfig = $reflection->getMethod('getBlockConfig');
//                     }
//                 }
//             }
//         }
//     }

//     public function init (ModuleManager $moduleManager)
//     {
//         $moduleManager->getEventManager()
//             ->getSharedManager()
//             ->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, array( $this, 'bootstrap' ));
//     }

//     public function bootstrap (MvcEvent $event)
//     {
//         /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
//         /* @var $moduleManager \Zend\ModuleManager\ModuleManager */

//         $serviceManager = $event->getApplication()->getServiceManager();
//         $config = $serviceManager->get('Config');

//         // load all the available adapters
//         if (isset($config['report_config']) && isset($config['report_config']['adapters'])) {
//             $adapters = $config['report_config']['adapters'];
//             foreach ($adapters as $adapter) {
//                 $adapter .= '\\Adapter';
//                 $class = new $adapter($serviceManager);
//                 if (class_exists($adapter, true) && is_callable(array( $class, 'getBlockConfig'))) {
//                     $blockConfig = call_user_func(array($class, 'getBlockConfig'));
//                     $config = \Zend\Stdlib\ArrayUtils::merge($config, $blockConfig);
// //                     $serviceManager->setService('Configuration', $config);

//                     $moduleManager = $serviceManager->get('ModuleManager');
//                     $module = $moduleManager->getModule(__NAMESPACE__);
//                 }
//             }
//         }
//     }

}