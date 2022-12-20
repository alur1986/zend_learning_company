<?php

namespace Authentication\Factory\Authentication;

use Authentication\Doctrine\Adapter\ObjectRepository;
use DoctrineModule\Service\Authentication\AdapterFactory as AbstractAdapterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AdapterFactory extends AbstractAdapterFactory implements
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
        /* @var $options \DoctrineModule\Options\Authentication */
        $options = $this->getOptions($serviceLocator, 'authentication');
        if (is_string($objectManager = $options->getObjectManager())) {
            $options->setObjectManager($serviceLocator->get($objectManager));
        }
        $repository = new ObjectRepository($options);

        return $repository;
    }
}