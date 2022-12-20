<?php

namespace Learner\Event;

use Savve\Mvc\View\Http\AbstractInjectTemplateListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;

class LearnerInjectTemplateListener extends AbstractInjectTemplateListener
{

    /**
     * Module name to use as prefix for template name
     *
     * @var string
     */
    protected $moduleNamespace = 'learner';

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach (EventManagerInterface $events)
    {
        /* @formatter:off */

        $sharedEventManager = $events->getSharedManager();
        $this->listeners[] = $sharedEventManager->attach('Learner\Controller', MvcEvent::EVENT_DISPATCH, [ $this, 'injectTemplate' ], -79);

        /* @formatter:on */
    }
}