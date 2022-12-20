<?php
/**
 * Get the role of the user currently logged in
 */
namespace Learner\Factory\Service;

use Savve\Session\Container as SessionContainer;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnerRoleFactory implements
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
        // get the current learner's learner ID
//        /* @var $authentication \Zend\Authentication\AuthenticationService */
//        $authentication = $serviceLocator->get('Zend\Authentication\AuthenticationService');
//        $identity = $authentication->getIdentity();
//        $learnerId = $identity instanceof Entity\Learner ? $identity['user_id'] : null;

        // get the current learner's role
        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceLocator->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();
        return $role;
    }
}