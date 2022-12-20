<?php

namespace Authorization\Factory\Guard;

use Authorization\Guard\RouteGuard as Guard;
use Savvecentral\Entity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\EventManager\EventInterface;

class RouteGuardFactory implements
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

        // get the route too !
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request     = $serviceLocator->getServiceLocator()->get('Request');
        /** @var \Zend\Mvc\Router\Http\TreeRouteStack $router */
        $router      = $serviceLocator->getServiceLocator()->get('Router');

        $route       = $router->match($request); // \Zend\Mvc\Router\RouteMatch
        if ($route == null) {
            // this fixes the /index.php exception
            header("Location: //".$_SERVER['SERVER_NAME']."/");
            return;
        }

        $routeName = $route->getMatchedRouteName();

        // get the permissions (rules) for the current logged in user and then the current route
        $permissions['role']  = $authorization->fetchPermissionsByRoleId($role['id'], 'route', $routeName);
        $permissions['route'] = $authorization->fetchPermissionsByRoute($role['id'], 'route', $routeName);
        $permissions['current_role'] = $role['id'];

        // create the guard
        $guard = new Guard($permissions);

        return $guard;
    }
}