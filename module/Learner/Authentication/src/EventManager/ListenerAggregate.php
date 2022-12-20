<?php

/**
 * @deprecated
 */
namespace Authentication\EventManager;

use Savve\EventListenerManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\View\ViewEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\ViewModel;

class ListenerAggregate extends AbstractListenerAggregate
{

    /**
     * Attach one or more listeners
     *
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach (EventManagerInterface $event)
    {
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManager */
        $sharedEventManager = $event->getSharedManager();
        $serviceManager = $this->getServiceManager();

        /* @formatter:off */

        $this->listeners[] = $event->attach(MvcEvent::EVENT_RENDER, [ $this, 'onRender' ], -100);

        /* @formatter:on */
    }

    /**
     * MvcEvent::EVENT_RENDER event listener
     *
     * @param MvcEvent $event
     */
    public function onRender (MvcEvent $event)
    {

        /* @var $target \Zend\Mvc\Application */
        /* @var $application \Zend\Mvc\Application */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        /* @var $layoutViewModel \Zend\View\Model\ViewModel */
        /* @var $actionViewModel \Zend\View\Model\ViewModel */
        $target = $event->getTarget();
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $routeMatch = $event->getRouteMatch();
        $layoutViewModel = $event->getViewModel();
        $actionViewModel = $event->getResult();

        // check if there is a current logged in user
        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
        if ($authentication->hasIdentity()) {
            $userId = (string) $authentication->getIdentity();

            /* @var $service \Learner\Service\LearnerService */
            $service = $serviceManager->get('Learner\Service');
            $learner = $service->findOneByUserId($userId);

            // check if learner has already been set in the layout view model
            if ($layoutViewModel instanceof ViewModel && !$layoutViewModel->getVariable('learner')) {
                $layoutViewModel->setVariable('learner', $learner);
            }

            // check if learner has already been set in the action view model
            if ($actionViewModel instanceof ViewModel && !$actionViewModel->getVariable('learner')) {
                $actionViewModel->setVariable('learner', $learner);
            }
        }
    }
}