<?php

namespace Authorization\Guard;

use Authorization\Guard\GuardManager\GuardProviderInterface;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

abstract class AbstractGuard implements
        GuardProviderInterface
{

    use ListenerAggregateTrait;

    /**
     * Event priority
     *
     * @var int
     */
    const EVENT_PRIORITY = -100;

    /**
     * MVC event to listen
     *
     * @var string
     */
    const EVENT_NAME = MvcEvent::EVENT_ROUTE;

    /**
     * Constant for guard that can be added to the MVC event result
     *
     * @var string
     */
    const UNAUTHORISED = 'guard-unauthorised';

    /**
     * Unauthorised access error message
     *
     * @var string
     */
    const UNAUTHORISED_MESSAGE = 'You are not authorised to access this resource';

    /**
     * Default access permission
     *
     * @var string
     */
    protected $permission = self::DENY;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $event
     *
     * @return void
     */
    public function attach (EventManagerInterface $event)
    {
        /* @formatter:off */
        $sharedEventManager = $event->getSharedManager();

        // attach MvcEvent listeners
		if (is_callable([ $this, 'onRoute'])) {
		    $this->listeners[] = $event->attach(MvcEvent::EVENT_ROUTE, [ $this, 'onRoute' ], static::EVENT_PRIORITY);
		}

        // navigation
    //    if (is_callable([ $this, 'onNavigation' ])) {
    //        $this->listeners[] = $sharedEventManager->attach('Zend\View\Helper\Navigation\AbstractHelper', 'isAllowed', [ $this, 'onNavigation' ]);
    //    }

        // view helper
        if (is_callable([ $this, 'onViewHelper' ])) {
            $this->listeners[] = $sharedEventManager->attach('Zend\View\Helper\AbstractHelper', 'isAllowed', [ $this, 'onViewHelper' ]);
        }

        // block/ViewModel
        if (is_callable([ $this, 'onViewModel' ])) {
            $this->listeners[] = $sharedEventManager->attach('Zend\View\Model\ViewModel', 'isAllowed', [ $this, 'onViewModel' ]);
        }

        // view renderer
        if (is_callable([ $this, 'onViewRender'])) {
            $this->listeners[] = $sharedEventManager->attach('Zend\View\Renderer\PhpRenderer', 'render', [ $this, 'onViewRender' ]);
        }

        /* @formatter:on */
    }

    /**
     * @private
     *
     * @param MvcEvent $event
     * @return void
     */
    public function onResult (MvcEvent $event)
    {
        // if user role have access to the current MVC event, then do not continue
        if ($this->isGranted($event)) {
            return;
        }

        /* @var $application \Zend\Mvc\Application */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */

        $application = $event->getApplication();
        $request = $application->getRequest();
        $response = $application->getResponse();
        $eventManager = $application->getEventManager();

        // only proceed if this an HTTP request
        if (!$request instanceof \Zend\Http\PhpEnvironment\Request) {
            return;
        }

        // set HTTP error status code to 401-Unauthorised
        $response->setStatusCode(401);

        // if you reach here, the user role does NOT have grant access to the MVC event
        $event->setError(self::UNAUTHORISED);
        $event->setParam('exception', new Exception\UnauthorisedException(static::UNAUTHORISED_MESSAGE, 401));

        // do not continue the rest of the event listeners
        $event->stopPropagation(true);

        // dispatch the DISPATCH_ERROR event
        $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }

    /**
     * Checks if the current user has grant access to current MVC event
     *
     * @param MvcEvent $event
     * @return boolean
     */
    public function isGranted ($event)
    {
        return $this->permission == self::ALLOW || $this->permission === true ? true : false;
    }

    /**
     * Get default permission
     *
     * @return string $permission
     */
    public function getPermission ()
    {
        return $this->permission;
    }

    /**
     * Set default permission
     *
     * @param string $permission
     * @return GuardProviderInterface
     */
    public function setPermission ($permission)
    {
        $this->permission = $permission;
        return $this;
    }
}