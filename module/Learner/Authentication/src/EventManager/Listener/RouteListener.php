<?php

namespace Authentication\EventManager\Listener;

use Savvecentral\Entity;
use Zend\Http;
use Zend\Console;
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
        try {
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
	        /* @var $authentication \Zend\Authentication\AuthenticationService */

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
            $routeName = $routeMatch->getMatchedRouteName();
            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
            $identity = $authentication->getIdentity();

            // do not proceed if not using HTTP request, ie, console request
            if (!($request instanceof Http\Request)) {
                return;
            }

            // inject the user ID in the router
            if (!$authentication->hasIdentity()) {
                return;
            }
            $userId = $identity instanceof Entity\Learner ? $identity['user_id'] : $identity;

            // set the current ID in the routeMatch
            if (!$routeMatch->getParam('user_id')) {
                $routeMatch->setParam('user_id', $userId);
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * MvcEvent::EVENT_ROUTE event route listener
     * Impersonate learner
     *
     * @param MvcEvent $event
     * @throws Exception
     */
    public function impersonate (MvcEvent $event)
    {
        try {
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
	        /* @var $authentication \Zend\Authentication\AuthenticationService */
            /* @var $service \Learner\Service\LearnerService */

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
            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
            $service = $serviceManager->get('Learner\Service');

            // only process if launched from the browser
            if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
                return;
            }

            // retrieve the authentication token from the route
            $authenticationToken = $routeMatch->getParam('authentication_token');

            // check if the authentication_token exists
            if ($authenticationToken) {

                // impersonate learner
                $learner = $service->impersonate($authenticationToken);
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}