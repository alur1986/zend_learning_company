<?php

namespace Report\EventProgressDetails\Factory\Service\Delegator;

use Savvecentral\Entity;
use Savve\Session\Container as SessionContainer;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterEventsByActivityIdDelegatorFactory implements
        DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName (ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /* @var $events \Doctrine\Common\Collections\ArrayCollection */
        $events = $callback();

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return $events;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        // only process this within this context
        if (!fnmatch('report/event-progress-details*', $routeName, FNM_CASEFOLD)) {
            return $events;
        }

        // check the route name for route variables/params
        if (preg_match('/^(?P<routename>report\/[a-z0-9-]+)\/(?P<action>[a-zA-Z0-9-]+)/i', $routeName, $matches)) {
            $parentRouteName = $matches['routename'];
            $action = $matches['action'];
            $sessionId = $routeMatch->getParam('session_id');
            $filterId = $routeMatch->getParam('filter_id');

            // if there is a session ID, then retrieve the data from the session
            if ($sessionId) {
                $session = new SessionContainer($sessionId);
                $activityId = $session['activity_id'];
            }

            // if editing the filter, retrieve from the filter data
            elseif ($filterId) {
                /* @var $filterService \Report\Service\FilterService */
                $filterService = $serviceLocator->get('Report\FilterService');
                $filter = $filterService->findOneFilterById($filterId);
                $activityId = $filter['activity_id'];
            }

            // if there are activity IDs, then filter the events collection based on the activity IDs
            if ($activityId) {
                $events = $events->filter(function  ($item) use( $activityId)
                {
                    return in_array($item['activity']['activity_id'], $activityId);
                });
            }
        }

        return $events;
    }
}