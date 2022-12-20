<?php

namespace Report\MyExperience\EventManager\Listener;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;

class RouteListener
{


    /**
     * Injects the logged in user's user_id onto the routeMatch
     *
     * @param MvcEvent $event
     */
    public function route (MvcEvent $event)
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
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
            return;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        $sessionId = $routeMatch->getParam('session_id');

        // only process this in the report module routes
        if (!fnmatch('report/*myexperience*', $routeName, FNM_CASEFOLD)) {
            return;
        }
        
        // check the route name for route variables/params
        if (preg_match('/^(?P<routename>report\/[a-z0-9-]+)(\/(?P<action>[a-zA-Z0-9-]+))?/i', $routeName, $matches)) {
            $parentRouteName = $matches['routename'];

            // start action
            if ($action === 'start') {

                $sessionId = $routeMatch->getParam('session_id') ?  : strtolower('r' . Stdlib\StringUtils::randomString(12));

                $newAction = 'reports/directory';
                $newRouteName = $parentRouteName . '/' . $newAction;

                $routeParams = [
                    'session_id' => $sessionId
                ];
                $routeOptions = [
                    'name' => $newRouteName
                ];

                $url = $router->assemble($routeParams, $routeOptions);
                $response->getHeaders()
                    ->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                return $response;
            }
        }
    }

}
