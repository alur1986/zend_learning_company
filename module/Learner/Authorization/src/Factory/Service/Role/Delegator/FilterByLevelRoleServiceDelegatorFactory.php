<?php

namespace Authorization\Factory\Service\Role\Delegator;

use Savvecentral\Entity;
use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;

class FilterByLevelRoleServiceDelegatorFactory implements
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
        $collection = call_user_func($callback);

        // get the current learner's learner ID
        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $identity = $authentication->getIdentity();
        $learnerId = $identity instanceof Entity\Learner ? $identity['user_id'] : null;

        // get the current learner's role
        /* @var $authorization \Authorization\Service\AuthorizationService */
        $authorization = $serviceLocator->get('Zend\Authorization\AuthorizationService');
        $role = $authorization->getRole();
        $roleName = $role['name'];
        $levelId = $role['level'] ? $role['level']['id'] : \Authorization\Stdlib\Authorization::LEVEL_1;

        // filter by role level with the current logged in learner's role as the maximum level role
        $collection = $collection->filter(function  ($item) use( $levelId)
        {
            return $item['level']['id'] <= $levelId;
        });

        return $collection;
    }
}