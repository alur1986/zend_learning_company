<?php

namespace Learner\Hydrator;

use Savve\Hydrator\AbstractAggregateHydrator;
use Zend\ServiceManager\ServiceLocatorInterface;

class Learner extends AbstractAggregateHydrator
{

    /**
     * Invoke aggregate hydrator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Learner\Learner
     */
    public function __invoke (ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        $hydratorManager = $this->getServiceLocator();
        $serviceManager = $hydratorManager->getServiceLocator();

        // define strategies to use
        $emptyStringStrategy = $serviceManager->get('EmptyStringStrategy');

        // attach some hydrators
        $hydrator = $hydratorManager->get('ArraySerializable');
        $hydrator->addStrategy('*', $emptyStringStrategy);
        $this->add($hydrator, -1);

        return $this;
    }
}