<?php

namespace Report\MyLocker\EventManager;

use Savve\Mvc\View\Http\AbstractInjectTemplateListener;

class InjectTemplateListener extends AbstractInjectTemplateListener
{

    /**
     * Module name to use as prefix for template name
     *
     * @var string
     */
    protected $moduleNamespace = 'report/mylocker';

    /**
     * Event identifier to use to attach this event listener
     *
     * @var string array
     */
    protected $eventIdentifier = 'Report\MyLocker\Controller';
}