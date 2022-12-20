<?php

namespace Report\Interactions\EventManager\Listener;

use Savve\Stdlib\Exception;
use Zend\View\ViewEvent;
use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;

class RouteListener
{

    private function isUnsupportedSite($siteId)
    {
        return ($siteId != 200108 && $siteId != 200123);
    }

    /**
     * MvcEvent::EVENT_ROUTE event listener
     *
     * @param MvcEvent $event
     * @throws Exception
     * @return boolean
     */
    public function route (MvcEvent $event)
    {
        try {

            $controller = $event->getTarget();
            $application = $event->getApplication();
            $eventManager = $application->getEventManager();
            $serviceManager = $application->getServiceManager();
            $routeMatch = $event->getRouteMatch();
            $request = $event->getParam('Request') ?  : $controller->getRequest();
            $response = $event->getParam('Response') ?  : $controller->getResponse();
            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
            $identity = $authentication->getIdentity();

            // only process if loaded in browser
            if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
                return;
            }

            if($routeMatch->getParam('REST')){
                return;
            }
            // current route name
            $routeName = $routeMatch->getMatchedRouteName();
            $allowed = true;

            $siteId = $routeMatch->getParam('site_id');
            // allow/deny interactions route
            if (fnmatch('report/interactions*', $routeName, FNM_CASEFOLD) && (empty($identity) || $this->isUnsupportedSite($siteId))) {
                $allowed = false;
            }

            // if not allowed, do not continue the event listener propagation
            if (!$allowed) {
                // do not continue the rest of the event listeners
                $event->stopPropagation(true);

                // Error 401 Unauthorised
                $errorCode = 401;
                $response->setStatusCode($errorCode);

                // set error messages
                $event->setError('error-page-not-found');
                $event->setParam('exception', new Exception\UnauthorisedException(sprintf('Access to this page is forbidden'), $errorCode));

                // dispatch the DISPATCH_ERROR event
                $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Zend\View\Helper\Navigation\AbstractHelper::isAllowed event listener
     *
     * @param Event $event
     * @throws Exception
     * @return void boolean
     */
    public function navigation (Event $event)
    {
        try {

            $menu = $event->getTarget();
            $page = $event->getParam('page');

            $serviceManager = $viewHelperManager = $menu->getServiceLocator();

            $application = $serviceManager->get('Application');
            $mvcEvent = $application->getMvcEvent();
            $routeMatch = $mvcEvent->getRouteMatch();
            $request = $serviceManager->get('Request');
            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
            $identity = $authentication->getIdentity();

            // only process if loaded in browser
            if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
                return;
            }

            // only process menus that are of Mvc type
            if (!($page instanceof \Zend\Navigation\Page\AbstractPage)) {
                return;
            }

            // get the current menu route name
            $routeName = $page instanceof \Zend\Navigation\Page\Mvc ? $page->getRoute() : $page->getUri();

            $siteId = $routeMatch->getParam('site_id');

            // default flag
            $allowed = true;


            // show/hide interactions menu
            if (fnmatch('report/interactions*', $routeName, FNM_CASEFOLD) && (empty($identity) || $this->isUnsupportedSite($siteId))) {
                $allowed = false;
            }

            // if not allowed, do not continue the event listener propagation
            if (!$allowed) {
                // do not continue the rest of the event listeners
                $event->stopPropagation(true);
            }

            return $allowed;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Zend\View\Renderer\PhpRenderer::render event listener
     *
     * @param Event $event
     * @throws Exception
     * @return boolean
     */
    public function render (Event $event)
    {
        try {
            $renderer = $event->getTarget();
            $model = $event->getParam('model');
            $helperManager = $renderer->getHelperPluginManager();
            $serviceManager = $helperManager->getServiceLocator();
            $application = $serviceManager->get('Application');
            $mvcEvent = $application->getMvcEvent();
            $routeMatch = $mvcEvent->getRouteMatch();
            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
            $identity = $authentication->getIdentity();

            $templateName = is_string($model) ? $model : null;
            if ($model instanceof \Zend\View\Model\ViewModel) {
                $templateName = $model->getTemplate();
            }

            // default flag
            $allowed = true;

            $siteId = $routeMatch->getParam('site_id');

            // show/hide interactions menu
            if (fnmatch('report/interactions*', $templateName, FNM_CASEFOLD) && (empty($identity) || $this->isUnsupportedSite($siteId))) {
                $allowed = false;
            }

            // if not allowed, do not continue the event listener propagation
            if (!$allowed) {
                // do not continue the rest of the event listeners
                $event->stopPropagation(true);
            }

            return $allowed;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
