<?php

namespace Authorization\Guard\GuardManager;

use Authorization\Stdlib\Authorization;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateInterface;

interface GuardProviderInterface extends
        ListenerAggregateInterface
{
    /**
     * Allow permission
     *
     * @var string
     */
    const ALLOW = Authorization::ALLOW;

    /**
     * Deny permission
     *
     * @var string
     */
    const DENY = Authorization::DENY;

    /**
     * Check if current role is granted access to the current MvcEvent
     *
     * @param EventInterface $event
     * @return boolean
     */
    public function isGranted ($event);
}