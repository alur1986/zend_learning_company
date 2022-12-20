<?php

namespace Authorization\Factory\View\Helper;

use Authorization\View\Helper\Role as Helper;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class RoleHelperFactory implements
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

        /* @var $service \Authorization\Service\AuthorizationService */
        $service = $serviceManager->get('Zend\Authorization\AuthorizationService');
        $role = $service->getRole();

        // instantiate the view helper
        $helper = new Helper($role);

        return $helper;
    }
}