<?php

namespace Report\IndividualLocker\Factory\Service\Delegator;

use Savvecentral\Entity;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterMyLockerCategoriesDelegatorFactory implements
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

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return $collection;
        }
        $routeName = $routeMatch->getMatchedRouteName();

        // THIS DELEGATOR IS ONLY FOR THE MYLOCKER
        if (!fnmatch('report/*locker*', $routeName, FNM_CASEFOLD)) {
            return $collection;
        }

        // filter the categories from Taxonomy to only show MyLocker term_group
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->andX($expr->eq('termGroup', 'mylocker')));
        $collection = $collection->matching($criteria);

        return $collection;
    }
}