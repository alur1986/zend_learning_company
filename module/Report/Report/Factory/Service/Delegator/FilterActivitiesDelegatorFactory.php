<?php

namespace Report\Factory\Service\Delegator;

use Savvecentral\Entity;
use Savve\Session\Container as SessionContainer;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterActivitiesDelegatorFactory implements
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
        $routeName  = $routeMatch->getMatchedRouteName();
        $controller = $routeMatch->getParam('controller');
        $action     = $routeMatch->getParam('action');

        // only process this within this context
        if (!fnmatch('report/*', $routeName, FNM_CASEFOLD)) {
            return $activities;
        }

        // only allow these learning activity statuses
        $status = [ 'active', 'migrated', 'inactive' ];
        $activities = $activities->filter(function  ($item) use( $status)
        {
            return in_array($item['status'], $status);
        });

        return $activities;
    }
}