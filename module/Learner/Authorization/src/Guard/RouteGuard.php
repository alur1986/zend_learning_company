<?php

namespace Authorization\Guard;

use Authorization\Stdlib\Authorization;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;

class RouteGuard extends AbstractGuard
{
    /**
     * MVC event to listen
     *
     * @var string
     */
    const EVENT_NAME = MvcEvent::EVENT_ROUTE;

    /**
     * Collection of rules for the current logged in role
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Constructor
     *
     * @param array|\Traversable $permissions
     */
    public function __construct ($rules)
    {
        ksort($rules);
        $this->rules = $rules;
    }

    /**
     * MvcEvent::EVENT_ROUTE event listener
     *
     * @param MvcEvent $event
     * @return void
     */
    public function onRoute (MvcEvent $event)
    {
        /* @var $application \Zend\Mvc\Application */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */

        $application = $event->getApplication();
        $request = $application->getRequest();
        $response = $application->getResponse();
        $eventManager = $application->getEventManager();
        $routeMatch = $event->getRouteMatch();

        // only proceed if this an HTTP request
        if (!$request instanceof \Zend\Http\PhpEnvironment\Request) {
            return;
        }
        $routeName = $routeMatch->getMatchedRouteName();

        // ignore route checks for api/v1/
        if (strpos($routeName, "api/v1/") !== false || strpos($routeName, "sso") !== false) {
            return true;
        }

        // set the default permission
        $granted = false;

        $currentRole = $this->rules['current_role'];
        // allow 'all' for Savve Admin
        if ($currentRole == 100007) return true;

        // the 'factory' fetches two sets of rules
        $rules = $this->rules['route'];

        foreach ($rules as $route => $permission) {
            if (fnmatch($route, $routeName, FNM_CASEFOLD)) {
                $permission = $rules[$route];

                // is the current role granted access?
                $granted = $permission === self::ALLOW ? true : false;
            }
        }

        // if allowed, skip
        if ($granted) {
            return;
        }

        // set HTTP error status code to 401-Unauthorised
    //    $response->setStatusCode(401);
        $response->setStatusCode(401);
        $response->sendHeaders();

        header("Location: /login");

        exit;

        // if you reach here, the user role does NOT have grant access to the MVC event
     //   $event->setError(self::UNAUTHORISED);
    //    $event->setParam('exception', new Exception\UnauthorisedException(static::UNAUTHORISED_MESSAGE, 401));

        // do not continue the rest of the event listeners
     //   $event->stopPropagation(true);

        // dispatch the DISPATCH_ERROR event
     //   $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

    /**
     * Navigation menu listener
     * Zend\View\Helper\Navigation\AbstractHelper
     */
    /*
    public function onNavigation (Event $event)
    {
        return true;
        /* @var $viewHelper \Zend\View\Helper\AbstractViewHelper */
        /* @var $pluginManager \Zend\View\HelperPluginManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager

        $viewHelper = $event->getTarget();
        $serviceManager = $pluginManager = $viewHelper->getServiceLocator();
        //$serviceManager = $pluginManager->getServiceLocator();

        // @todo get this default $granted value from the config
        $granted = false;
        $rules = $this->rules['role'];

        // get the current navigation page
        /* @var $page \Zend\Navigation\Page\Mvc
        $page = $event->getParam('page');

        // process only menu's that are Mvc type
        if ($page instanceof \Zend\Navigation\Page\Mvc) {
            $routeName = $page->getRoute();
            $allow = self::ALLOW;

            // find the current page route from the rules
            $found = array_filter($rules, function  ($permission, $route) use( $routeName)
            {
                return fnmatch($route, $routeName, FNM_CASEFOLD) && strtolower($permission) === 'allow';
            }, ARRAY_FILTER_USE_BOTH);

            // if found, then allow permission
            if ($found) {
                $granted = true;
            }

            // do not process other event listeners after this
            if ($granted === false) {
                $event->stopPropagation(true);
            }

            return $granted;
        }
        return true;
    } */

    /**
     * Zend\View\Helper\* event listener
     *
     * @return boolean $accepted True if current role is granted access to the route, False otherwise
     */
    public function onViewHelper (Event $event)
    {
        /* @var $viewHelper \Zend\View\Helper\AbstractViewHelper */
        /* @var $pluginManager \Zend\View\HelperPluginManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

        $viewHelper = $event->getTarget();
        $pluginManager = $viewHelper->getServiceLocator();
        $serviceManager = $pluginManager->getServiceLocator();

        $granted = true;
        $rules = $this->rules;

        // get the param from view helper
        $routeName = $event->getParam('route');

        // process only view helpers that have route param
        if ($routeName) {
            // find the current page route from the rules
            $found = array_filter($rules, function  ($permission, $route) use( $routeName)
            {
                return fnmatch($route, $routeName, FNM_CASEFOLD) && strtolower($permission) === 'deny';
            }, ARRAY_FILTER_USE_BOTH);

            // if found, then deny permission
            if ($found) {
                $granted = false;
            }
        }

        // do not process other event listeners after this
        if ($granted === false) {
            $event->stopPropagation(true);
        }

        return $granted;
    }
}
