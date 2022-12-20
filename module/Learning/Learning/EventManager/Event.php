<?php

namespace Learning\EventManager;

use Savve\EventManager\Event as AbstractEvent;

class Event extends AbstractEvent
{
    const EVENT_DUPLICATE = 'duplicate';
}