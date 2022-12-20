<?php

namespace Authorization\Factory\Service\Role;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllRolesServiceFactory implements
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
        $roles = $service->findAllRoles();

        return $roles ?  : false;
    }
}