<?php

namespace Authorization\Factory\Guard;

use Authorization\Guard\ViewModelGuard as Guard;
use Savvecentral\Entity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ViewModelGuardFactory implements
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
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
        $identity = $authentication->getIdentity();
        $learnerId = $identity instanceof Entity\Learner ? $identity['user_id'] : $identity;

        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceManager->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();

        // get the permisions (rules) for the current logged in user
        $permissions = $authorization->fetchPermissionsByRoleId($role['id'], 'block');

        // create the guard
        $guard = new Guard($permissions);

        return $guard;
    }
}