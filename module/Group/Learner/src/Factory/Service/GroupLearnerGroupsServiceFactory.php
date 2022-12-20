<?php

namespace Group\Learner\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GroupLearnerGroupsServiceFactory implements
        FactoryInterface
{

    /**
     * Find ALL groups by learner ID
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return false;
        }

        $userId = $routeMatch->getParam('user_id');
        if (!$userId) {
            return false;
        }

        /* @var $service \Group\Learner\Service\GroupLearnerService */
        $service = $serviceLocator->get('Group\Learner\Service');
        $groups = $service->findAllGroupsByLearnerId($userId) ?  : new ArrayCollection();

        return $groups;
    }
}