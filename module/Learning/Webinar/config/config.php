<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Webinar\Service' => 'Webinar\Factory\Service\WebinarServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Webinar\Controller\Manage' => 'Webinar\Controller\ManageController',
            'Webinar\Controller\Event' => 'Webinar\Controller\EventController',
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Webinar\Form\Create' => 'Webinar\Factory\Form\CreateWebinarFormFactory',
            'Webinar\Form\Update' => 'Webinar\Factory\Form\UpdateWebinarFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'identifier' => 'Learning\Service\LearningService',
            'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
            'callback' => 'Webinar\EventManager\Listener\DuplicateActivityListener'
        ]
    ]
];