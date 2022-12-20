<?php

namespace Group\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllGroupsFactory implements
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

        // get all the groups
        $groups = $service->findAllGroupsBySiteId($siteId) ?  : new ArrayCollection();

        return $groups;
    }
}