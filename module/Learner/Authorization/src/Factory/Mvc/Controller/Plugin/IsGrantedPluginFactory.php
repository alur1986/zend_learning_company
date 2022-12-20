<?php

namespace Authorization\Factory\Mvc\Controller\Plugin;

use Authorization\Mvc\Controller\Plugin\IsGranted as Plugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class IsGrantedPluginFactory implements
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

        // get the current user's role
        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceManager->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();

        // get the permisions (rules) for the current logged in user
        $permissions = $authorization->fetchPermissionsByRoleId($role['id']);
        ksort($permissions);

        // create the plugin
        $plugin = new Plugin($permissions);

        return $plugin;
    }
}