<?php

namespace Report\MyLearning\Factory\Service\Delegator;

use Savvecentral\Entity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterEventActivitiesDelegatorFactory implements
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
        /* @var $activities \Doctrine\Common\Collections\ArrayCollection */
        $activities = $callback();

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return $activities;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        // only process this within this context
        if (!fnmatch('report/mylearning*', $routeName, FNM_CASEFOLD)) {
            return $activities;
        }

        // check the route name for route variables/params
        if (preg_match('/^(?P<routename>report\/[a-z0-9-]+)\/(?P<action>[a-zA-Z0-9-]+)/i', $routeName, $matches)) {
            $parentRouteName = $matches['routename'];
            $action = $matches['action'];
            $sessionId = $routeMatch->getParam('session_id');

            // filter only the event-type learning activities
            $types = ['face-to-face', 'written-assessment', 'on-the-job-assessment', 'webinar'];
            $activities = $activities->filter(function($item)use($types){
            	return in_array($item['activityType'], $types);
            });
        }

        return $activities;
    }
}