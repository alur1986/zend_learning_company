<?php

namespace Learner\Factory\View\Helper;

use Learner\View\Helper\Learner as Helper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnerViewHelperFactory implements
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
        $serviceManager = $serviceLocator->getServiceLocator();
        $authenticationService = $serviceManager->get('Zend\Authentication\AuthenticationService');

        // if no learner is logged in , return false
        if (!$authenticationService->hasIdentity()) {
            return false;
        }

        $userId = (string) $authenticationService->getIdentity();

        /* @var $service \Learner\Service\LearnerService */
        $service = $serviceManager->get('Learner\Service');
        $learner = $service->findOneByUserId($userId);

        $helper = new Helper($learner);
        return $helper;
    }
}