<?php

namespace Authentication\Factory\Authentication;

use Authentication\Doctrine\Storage\ObjectRepository;
use DoctrineModule\Service\Authentication\StorageFactory as AbstractStorageFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class StorageFactory extends AbstractStorageFactory implements
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

        if (is_string($storage = $options->getStorage())) {
            $options->setStorage($serviceLocator->get($storage));
        }

        return new ObjectRepository($options);
    }
}