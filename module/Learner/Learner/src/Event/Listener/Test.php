<?php

namespace Learner\Event\Listener;

use Zend\Mvc\MvcEvent;

class Test
{

    /**
     * MvcEvent:EVENT_ROUTE event listener
     *
     * @param MvcEvent $event
     */
    public function routeTest (MvcEvent $event)
    {
        /* @var $application \Zend\Mvc\Application */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $controllerPluginManager \Zend\Mvc\Controller\PluginManager */
        /* @var $viewManager \Zend\Mvc\View\Http\ViewManager */
        /* @var $viewHelperManager \Zend\View\HelperPluginManager */
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */

        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');
        $viewManager = $serviceManager->get('ViewManager');
        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $router = $event->getRouter();
        $request = $event->getRequest();
        $response = $event->getResponse();
        $routeMatch = $event->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        $actionName = $routeMatch->getParam('action');
        $controllerName = $routeMatch->getParam('controller');
        $controllerNamespace = substr($controllerName, 0, strpos($controllerName, '\\'));
        $moduleNamespace = substr(__NAMESPACE__, 0, strpos(__NAMESPACE__, '\\'));
        $config = $serviceManager->get('Config');
    }
}