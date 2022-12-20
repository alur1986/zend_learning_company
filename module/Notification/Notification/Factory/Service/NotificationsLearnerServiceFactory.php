<?php

namespace Notification\Factory\Service;

use Notification\Service\NotificationService as Service;
use Savvecentral\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class NotificationsLearnerServiceFactory implements
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
        $identity = $authentication->getIdentity();
        if (!$identity) {
            return false;
        }
        $learnerId = ($identity instanceof Entity\Learner) ? $identity['user_id'] : $identity;

        /* @var $service \Notification\Service\NotificationService */
        $service = $serviceLocator->get('Notification\Service');
        $notifications = $service->findAllActiveByLearnerId($learnerId);

        return $notifications;
    }
}