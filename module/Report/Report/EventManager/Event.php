<?php

namespace Report\EventManager;

use Savve\EventManager\Event as AbstractEvent;

class Event extends AbstractEvent
{
    const EVENT_REPORT_PRE = 'report.pre';
    const EVENT_REPORT_POST = 'report.post';
}