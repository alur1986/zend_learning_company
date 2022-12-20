<?php

namespace Authorization\Factory\Service\Role;

use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OneRoleServiceFactory implements
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

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof RouteMatch)) {
            return false;
        }
        $roleId = $routeMatch->getParam('role_id');
        $role = $service->findOneRoleById($roleId);

        return $role;
    }
}