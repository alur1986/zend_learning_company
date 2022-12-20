<?php

namespace Notification\Factory\Service;

use Notification\Service\NotificationService as Service;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class NotificationsAllServiceFactory implements
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
        /* @var $service \Notification\Service\NotificationService */
        $service = $serviceLocator->get('Notification\Service');
        $notifications = $service->findAll();
        return $notifications;
    }
}