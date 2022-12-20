<?php

namespace Learner;

use Savve\Mvc\AbstractModule;
use Zend\Http;
use Zend\Console;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;

class Module extends AbstractModule
{

    /**
     * Define module dependencies
     *
     * @var array
     */
    protected $moduleDependencies = [
        'Savve'
    ];

    /**
     * Listen to the bootstrap event
     *
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
        /* @var $request \Zend\Http\PhpEnvironment\Request */

        $application = $event->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
    //    $sharedEventManager = $eventManager->getSharedManager();
        $request = $serviceManager->get('Request');
        $options = $serviceManager->get('Authorization\Options');

        // do not proceed if not using HTTP request, ie, console request
        if (!($request instanceof Http\Request)) {
            return;
        }

        // check if the Authorization sub-module is "enabled"
        if ($options->getEnabled() !== true) {
            return;
        }

        /** This will prevent the "PHP Fatal error:  Uncaught exception 'Zend\ServiceManager\Exception\ServiceNotCreatedException'
         * with message 'The factory was called but did not return an instance. authorizationguardroute , Authorization\Guard\Route'"
         */
        if (strpos($_SERVER['REQUEST_URI'], '/hello.txt') !== false || strpos($_SERVER['REQUEST_URI'], '/loading.gif') !== false || strpos($_SERVER['REQUEST_URI'], '/robots.txt') !== false || strpos($_SERVER['REQUEST_URI'], '/css/') !== false || strpos($_SERVER['REQUEST_URI'], '.css') !== false || strpos($_SERVER['REQUEST_URI'], '.xml') !== false || strpos($_SERVER['REQUEST_URI'], '/savve/help/') !== false || strpos($_SERVER['REQUEST_URI'], '/savvy/help/') !== false || strpos($_SERVER['REQUEST_URI'], '.pdf') !== false || strpos($_SERVER['REQUEST_URI'], '.swf') !== false) {
            if (strpos($_SERVER['REQUEST_URI'], '/learning/resource/file/delete/') === false && strpos($_SERVER['REQUEST_URI'], '/learning/resource/file/download/') === false) {
                return;
            }
        }

        // create the Authorization instance register
        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceManager->get('Zend\Authorization\AuthorizationService');
        \Authorization\Stdlib\Authorization::setInstance($authorization);

        $guards = $serviceManager->get('Authorization\Guards');
        if ($guards) {
            foreach ($guards as $guard) {
                try {
                    $eventManager->attachAggregate($guard);
             //       $guard->attach($eventManager);
                } catch(\Exception $e) {
                    throw $e;
                }
            }
        }
    }
}