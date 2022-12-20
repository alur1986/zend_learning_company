<?php

namespace Authorization\Factory\Service;

use Authorization\Service\AuthorizationService as Service;
use Savvecentral\Entity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AuthorizationServiceFactory implements
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
        // doctrine entity manager
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        // authentication service
        $authentication = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $identity = $authentication->getIdentity();
        $learnerId = $identity instanceof Entity\Learner ? $identity['user_id'] : null;

        // authorization option service
        /* @var $options \Authorization\Service\Options */
        $options = $serviceLocator->get('Authorization\Options');

        // instantiate the authorization service
        $service = new Service($entityManager);

        // check if the Authorization submodule is "enabled"
        if ($options->getEnabled() !== true) {
            return $service;
        }

        // set the default role if not logged in
        if (!$identity) {
            $name = $options->getDefaultRole();
            $service->setRole($name);
        }

        // if learner is logged in, check if they have a role assigned
        if ($identity) {
            $role = $service->findOneRoleByLearnerId($learnerId);
            $role ? $service->setRole($role) : null;

            // if no role was assigned, then use the default logged in role
            if (!$role) {
                $name = $options->getDefaultLoggedInRole();
                $service->setRole($name);
            }
        }

        return $service;
    }
}