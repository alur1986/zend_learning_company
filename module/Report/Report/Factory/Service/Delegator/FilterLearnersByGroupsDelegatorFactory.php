<?php

namespace Report\Factory\Service\Delegator;

use Savvecentral\Entity;
use Savve\Session\Container as SessionContainer;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterLearnersByGroupsDelegatorFactory implements
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
        /* @var $learners \Doctrine\Common\Collections\ArrayCollection */
        $learners = $callback();

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return $learners;
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        // check the route name for route variables/params
        if (preg_match('/^(?P<routename>report\/[a-z0-9-]+)\/(?P<action>[a-zA-Z0-9-]+)/i', $routeName, $matches)) {
            $parentRouteName = $matches['routename'];
            $sessionId = $routeMatch->getParam('session_id');
            $filterId = $routeMatch->getParam('filter_id');

            // only process collection in the "learners" sub-route (action)
            if ($action === 'learners') {
                $groupIds = null;

                // if there is a session ID, then retrieve the data from the session
                if ($sessionId) {
                    $session = new SessionContainer($sessionId);
                    $groupIds = $session['group_id'];
                }
                // if editing the filter, retrieve from the filter data
                elseif ($filterId) {
                    /* @var $filterService \Report\Service\FilterService */
                    $filterService = $serviceLocator->get('Report\FilterService');
                    $filter = $filterService->findOneFilterById($filterId);
                    $groupIds = $filter['group_id'];
                }

                // no selected group IDs
                if (!$groupIds) {
                    return $learners;
                }

                /* @var $groupLearnerService \Group\Learner\Service\GroupLearnerService */
                $groupLearnerService = $serviceLocator->get('Group\Learner\Service');
                $groupLearners = $groupLearnerService->fetchAllTheLearnersInGroupId($groupIds,['new','active','inactive']);
                $learnerIds = array_map('current', $groupLearners);

                if ($learners instanceof Collection) {
                    // filter the collection with learner IDs in the group learners
                    $learners = $learners->filter(function  ($item) use( $learnerIds)
                    {
                        return in_array($item['user_id'], $learnerIds);
                    });
                }
            }
        }
        return $learners;
    }
}