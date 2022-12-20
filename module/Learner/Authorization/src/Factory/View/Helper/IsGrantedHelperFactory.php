<?php

namespace Authorization\Factory\View\Helper;

use Authorization\View\Helper\IsGranted as Helper;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class IsGrantedHelperFactory implements
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

        // get the route too !
        /** @var \Zend\Http\PhpEnvironment\Request $request */
    //    $request     = $serviceLocator->getServiceLocator()->get('Request');
        /** @var \Zend\Mvc\Router\Http\TreeRouteStack $router */
    //    $router      = $serviceLocator->getServiceLocator()->get('Router');
    //    $route       = $router->match($request); // \Zend\Mvc\Router\RouteMatch

        // get the current user's role
        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceManager->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();

        // get the permisions (rules) for the current logged in user
        $permissions['block'] = $authorization->fetchPermissionsByOneRoleId($role['id']);
        $permissions['route'] = $authorization->fetchPermissionsByRoleId($role['id']);

        // instantiate the view helper
        $helper = new Helper($permissions);

        return $helper;
    }
}