<?php

namespace Authorization\Factory\Service\Resource\Delegator;

use Savvecentral\Entity;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterByLevelResourceServiceDelegatorFactory implements
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
        /* @var $collection \Doctrine\Common\Collections\ArrayCollection */
        $collection = $callback();

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return $collection;
        }
        $routeName = $routeMatch->getMatchedRouteName();

        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceLocator->get('Zend\Authorization\AuthorizationService');

        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        if ($authentication->hasIdentity()) {
            $identity = $authentication->getIdentity();
            $learnerId = $identity instanceof Entity\Learner ? $identity['user_id'] : $identity;
        }

        // we are only running this in the authorization route
        if (fnmatch('secure*', $routeName, FNM_CASEFOLD)) {
            if ($roleName = $routeMatch->getParam('role')) {
                $role = $authorization->findOneRoleByName($roleName);
                $level = $role['level'] ? $role['level']['id'] : 1;

                // filter the resources according to the level
                if ($collection instanceof Collection) {
                    $collection = $collection->filter(function  ($item) use( $level)
                    {
                        // return only resources that have the same or lower level as the current role level
                        return $item['level']['id'] <= $level;
                    });
                }
            }
        }

        return $collection;
    }
}