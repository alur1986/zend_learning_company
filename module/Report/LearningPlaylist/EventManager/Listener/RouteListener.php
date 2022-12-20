<?php

namespace Report\LearningPlaylist\EventManager\Listener;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;

class RouteListener
{

    protected $routeNamePrefix = 'report/learning-playlist';

    /**
     * Injects the logged in user's user_id onto the routeMatch
     *
     * @param MvcEvent $event
     */
    public function __invoke (MvcEvent $event)
    {
        /* @var $controller \Zend\Mvc\Controller\AbstractActionController */
        /* @var $application \Zend\Mvc\Application */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $viewManager \Zend\Mvc\View\Http\ViewManager */
        /* @var $viewHelperManager \Zend\View\HelperPluginManager */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        /* @var $controllerPluginManager \Zend\Mvc\Controller\PluginManager */
        /* @var $translator \Zend\I18n\Translator\Translator */

        $controller = $event->getTarget();
        $application = $event->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $viewManager = $serviceManager->get('ViewManager');
        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $renderer = $viewHelperManager->getRenderer();
        $routeMatch = $event->getRouteMatch();
        $router = $serviceManager->get('Router');
        $request = $event->getParam('request') ?  : $controller->getRequest();
        $response = $event->getParam('response') ?  : $controller->getResponse();
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');
        $translator = $serviceManager->get('Translator');

        // only process if routeMatch is set
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return;
        }
        $routeName = $routeMatch->getMatchedRouteName();

        // check the route name for route variables/params
        if (preg_match('/^(?P<routename>report\/[a-z0-9-]+)\/(?P<action>[a-zA-Z0-9-]+)/i', $routeName, $matches)) {
            $parentRouteName = $matches['routename'];
            $action = $matches['action'];
            $sessionId = $routeMatch->getParam('session_id');

            // all other actions, if there are no session ID, redirect to the start
            if ($action === 'learners' && $sessionId) {
                // do something
            }
        }
    }
}