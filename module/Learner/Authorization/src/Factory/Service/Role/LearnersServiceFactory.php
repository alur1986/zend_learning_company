<?php

namespace Authorization\Factory\Service\Role;

use Doctrine\Common\Collections\Collection;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnersServiceFactory implements
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

        // get the current role
        $role = $service->findOneRoleById($roleId);

        // current role level
        $level = isset($role['level']) ? $role['level']['id'] : 0;

        // get the role's learners
        $learners = $service->findAllLearnersInRole($roleId);

        // only show learners that are active/new
        if ($learners instanceof Collection) {
            $learners = $learners->filter(function  ($item)
            {
                return in_array($item['status'], [
                    'new',
                    'active'
                ]);
            });
        }

        // company level (LEVEL_4) and below will only show learners within the current site
        $siteId = $routeMatch->getParam('site_id');
        if ($level <= \Authorization\Stdlib\Authorization::LEVEL_6 && $siteId) {
            if ($learners instanceof Collection) {
                $learners = $learners->filter(function  ($item) use( $siteId)
                {
                    $site = $item['site'];
                    return $site['site_id'] === $siteId;
                });
            }
        }

        // platform level (level_7) and above will show all learners within that platform
        $platformId = $routeMatch->getParam('platform_id');
        if ($level >= \Authorization\Stdlib\Authorization::LEVEL_7 && $platformId) {
            if ($learners instanceof Collection) {
                $learners = $learners->filter(function  ($item) use( $platformId)
                {
                    $site = $item['site'];
                    $platform = $site['platform'];
                    return $platform['platform_id'] === $platformId;
                });
            }
        }

        return $learners;
    }
}