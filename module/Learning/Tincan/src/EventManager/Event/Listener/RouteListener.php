<?php

namespace Tincan\EventManager\Event\Listener;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;

class RouteListener
{

    /**
     * Invoke the event listener
     *
     * @param MvcEvent $event
     */
    public function route (MvcEvent $event)
    {
        /* @var $application \Zend\Mvc\Application */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */

        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $request = $application->getRequest();
        $response = $application->getResponse();
        $routeMatch = $event->getRouteMatch();

        // if run from the console, do not continue
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
            return;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $itemId = $routeMatch->getParam('item_id');

        // only process this if the route name matches the pattern for Tincan items
        if (fnmatch('learning/tincan/*item*', $routeName, FNM_CASEFOLD) && $itemId) {
            /* @var $service \Tincan\Service\TincanService */
            $service = $serviceManager->get('Tincan\Service');
            $item = $service->findOneItemById($itemId);
            $activity = $item['activity'];
            $activityId = $activity['activity_id'];
            if (!$routeMatch->getParam('activity_id')) {
                $routeMatch->setParam('activity_id', $activityId);
            }
        }
    }
}