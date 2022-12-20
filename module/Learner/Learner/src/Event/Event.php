<?php

namespace Learner\Event;

use Savve\EventManager\Event as AbstractEvent;

class Event extends AbstractEvent
{
    const EVENT_REGISTER = 'register';
    const EVENT_REGISTERED = 'registered';
    const EVENT_CHANGE_PASSWORD = 'change-password';
    const EVENT_FORGOT_PASSWORD = 'forgot-password';
    const EVENT_RESET_PASSWORD = 'reset-password';
    const EVENT_RESET_PASSWORD_SUCCESS = 'reset-password-success';
    const EVENT_IMPERSONATE = 'impersonate';
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_EMPLOYMENT = 'employment';
    const EVENT_BULK = 'bulk';
    const EVENT_ACTIVATE = 'activate';

    const ERROR_CODE_MAX_REACHED = 'licence';
}