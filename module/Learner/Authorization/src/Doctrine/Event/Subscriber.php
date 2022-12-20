<?php

namespace Authorization\Doctrine\Event;

use Authorization\Stdlib\Authorization;
use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber as DoctrineSubscriberInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Subscriber implements
        DoctrineSubscriberInterface,
        ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents ()
    {
        return [
            Events::postLoad
        ];
    }

    /**
     * PostLoad event subscriber
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad (LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        // process the Entity\AccessRoles entity
        if ($entity instanceof Entity\AccessRoles) {
            $serviceManager = $this->getServiceLocator();

            /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
            $routeMatch = $serviceManager->get('Application')
                ->getMvcEvent()
                ->getRouteMatch();
            if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
                return;
            }
            $siteId = $routeMatch->getParam('site_id');
            $platformId = $routeMatch->getParam('platform_id');

            /* @var $options \Authorization\Service\Options */
            $options = $serviceManager->get('Authorization\Options');
            $entity['default_permission'] = $options->getDefaultPermission();

            // current role level
            $level = $entity['level'] ? $entity['level']['id'] : 0;

            // filter learners that are active
            $learners = isset($entity['learners']) ? $entity['learners'] : null;
            if ($learners instanceof Collection) {
                $entity['learners'] = $learners->filter(function  ($item)
                {
                    return in_array($item['status'], [
                        'new',
                        'active'
                    ]);
                });
            }

            // if current role level is below the platform admin (level_5), show only company-level data
            if ($level <= \Authorization\Stdlib\Authorization::LEVEL_4) {
                // filter learners that belong only to the current site
                $learners = isset($entity['learners']) ? $entity['learners'] : null;
                if ($learners instanceof Collection) {
                    $entity['learners'] = $learners->filter(function  ($item) use( $siteId)
                    {
                        $site = $item['site'];
                        return $site['site_id'] === $siteId;
                    });
                }

                // filter the rules that belong only to the current site
                $rules = isset($entity['rules']) ? $entity['rules'] : null;
                if ($rules instanceof Collection) {
                    $entity['rules'] = $rules->filter(function  ($item) use( $siteId)
                    {
                        $site = $item['site'];
                        return $site['site_id'] === $siteId || $site['site_id'] === null;
                    });
                }
            }

            /* @var $authorization \Authorization\Service\AuthorizationService */
            $authorization = $serviceManager->get('Zend\Authorization\AuthorizationService');
            $role = $authorization->getRole();

            // if current logged in user's role is platform-admin (LEVEL_5), then show only the learners within that platform
            if ($level == \Authorization\Stdlib\Authorization::LEVEL_5) {
                // filter learners that belong only to the current site
                $learners = isset($entity['learners']) ? $entity['learners'] : null;
                if ($learners instanceof Collection) {
                    $entity['learners'] = $learners->filter(function  ($item) use( $platformId)
                    {
                        $site = $item['site'];
                        $platform = $site['platform'];
                        return $platform['platform_id'] === $platformId;
                    });
                }
            }
        }
    }
}