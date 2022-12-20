<?php

namespace Learner\Factory\Service;

use Savve\Session\Container as SessionContainer;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnerFactory implements
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
        /* @var $authenticationService \Zend\Authentication\AuthenticationService */
        $authenticationService = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        if ($authenticationService->hasIdentity()) {
            $identity = (string) $authenticationService->getIdentity();

            // load the learner model
            /* @var $service \Learner\Service\LearnerService */
            $service = $serviceLocator->get('Learner\Service');
            $learner = $service->findOneByUserId($identity);

            return $learner ?  : false;
        }

        return false;
    }
}