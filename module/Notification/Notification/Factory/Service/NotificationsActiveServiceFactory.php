<?php

namespace Notification\Factory\Service;

use Notification\Service\NotificationService as Service;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class NotificationsActiveServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ArrayCollection
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $userId = $authentication->getIdentity();
        if (!$userId) {
            return false;
        }

        /* @var $service \Notification\Service\NotificationService */
        $service = $serviceLocator->get('Notification\Service');
        $notifications = $service->findAllActiveByLearnerId($userId);

        return $notifications;
    }
}