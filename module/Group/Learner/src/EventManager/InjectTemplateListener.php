<?php

namespace Group\Learner\EventManager;

use Savve\Mvc\View\Http\AbstractInjectTemplateListener;

class InjectTemplateListener extends AbstractInjectTemplateListener
{

    /**
     * Module name to use as prefix for template name
     *
     * @var string
     */
    protected $moduleNamespace = 'group/learner';

    /**
     * Event identifier to use to attach this event listener
     *
     * @var string array
     */
    protected $eventIdentifier = 'Group\Learner\Controller';
}