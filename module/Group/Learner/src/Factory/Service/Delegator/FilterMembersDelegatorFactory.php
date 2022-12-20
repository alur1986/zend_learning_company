<?php

namespace Group\Learner\Factory\Service\Delegator;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterMembersDelegatorFactory implements
        DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName (ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /* @var $collection \Doctrine\Common\Collections\ArrayCollection */
        $collection = $callback();

        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return $collection;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $groupId = $routeMatch->getParam('group_id');

        // THIS DELEGATOR IS ONLY TO DISPLAY THE CURRENT GROUP'S LEARNERS
        if (!(fnmatch('group/learner/directory*', $routeName, FNM_CASEFOLD) && $groupId)) {
            return $collection;
        }

        if ($collection instanceof Collection) {
            /* @var $groupLearnerService \Group\Learner\Service\GroupLearnerService */
            $groupLearnerService = $serviceLocator->get('Group\Learner\Service');
            $groupLearners = $groupLearnerService->fetchAllLearnersInGroupId($groupId);
            $learnerIds = array_map(function  ($item)
            {
                return $item['user_id'];
            }, $groupLearners);

            $collection = $collection->filter(function  ($item) use( $learnerIds)
            {
                return in_array($item['user_id'], $learnerIds);
            });
        }

        return $collection;
    }
}