<?php

namespace Authorization\Factory\Service\Role;

use Doctrine\Common\Collections\Collection;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class RulesServiceFactory implements
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
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return false;
        }
        $roleId = $routeMatch->getParam('role_id');
        if (!$roleId) {
            return false;
        }

        // get the current role being viewed
        $role = $service->findOneRoleById($roleId);

        // current viewed role level
        $level = isset($role['level']) ? $role['level']['id'] : 0;

        // get all the rules for the current role
        $rules = $service->findAllRulesByRoleId($roleId);

        // company level (LEVEL_4) and below will only show rules within the current site
        $siteId = $routeMatch->getParam('site_id');
        if ($level <= \Authorization\Stdlib\Authorization::LEVEL_4 && $siteId) {
            if ($rules instanceof Collection) {
                $rules = $rules->filter(function  ($item) use( $siteId)
                {
                    $site = $item['site'];
                    return $site['site_id'] === $siteId || $site['site_id'] === null;
                });
            }
        }

        // platform level (level_5) and above will show all learners within that platform
        $platformId = $routeMatch->getParam('platform_id');
        if ($level >= \Authorization\Stdlib\Authorization::LEVEL_5 && $platformId) {
            if ($rules instanceof Collection) {
                $rules = $rules->filter(function  ($item) use( $platformId)
                {
                    $site = $item['site'];
                    $platform = $site['platform'];
                    return $platform['platform_id'] === $platformId || $site == NULL;
                });
            }
        }

        return $rules;
    }
}