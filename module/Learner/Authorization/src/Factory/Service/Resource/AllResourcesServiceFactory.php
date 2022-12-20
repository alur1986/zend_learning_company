<?php

namespace Authorization\Factory\Service\Resource;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllResourcesServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $service \Authorization\Service\AuthorizationService */
        $service = $serviceLocator->get('Zend\Authorization\AuthorizationService');
        $resources = $service->findAllResources();

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if ($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            // get the current role
            $roleId = $routeMatch->getParam('role_id');
            if (!$roleId) {
                return $resources;
            }
            $role = $service->findOneRoleById($roleId);
            $level = isset($role['level']) ? $role['level']['id'] : 0;

            // only show resources that are assigned the same level or lower as the role
            // !! in some cases a learner needs access to the low level of a route - it seems that
            // by default that means they get access to all child resources ofthat route
            // !! we need to be able to deny a low level role access to an item allowed for a high level role
        /*    $resources = $resources->filter(function  ($item) use( $level)
            {
                return $item['level']['id'] <= $level;
            }); */
        }

        return $resources ?  : false;
    }
}