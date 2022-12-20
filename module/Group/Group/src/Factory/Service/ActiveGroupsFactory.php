<?php

namespace Group\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ActiveGroupsFactory implements
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
        /* @var $service \Group\Service\GroupService */
        $service = $serviceLocator->get('Group\Service');

        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');
        $groups = $service->findAllActiveGroupsBySiteId($siteId) ?  : new ArrayCollection();
        return $groups;
    }
}