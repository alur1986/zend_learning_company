<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Agent\Service' => 'Agent\Factory\Service\AgentServiceFactory',
            'Agents\All'     => 'Agent\Factory\Service\AgentAllServiceFactory',
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Agent\Controller\Manage' => 'Agent\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENT MANAGER
     */
    'form_elements' => [
        'factories' => [
            'Agent\Form\New' => 'Agent\Factory\Form\NewFormFactory',
            'Agent\Form\Edit' => 'Agent\Factory\Form\EditFormFactory'
        ],
        'initializers' => [
            'Agent\Form\Initializer'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => ['Agent\EventManager\Listener\RouteListener', 'routeListener'],
        ]
    ],

    /**
     * VIEW HELPERS
     */
    'view_helpers' => [
        'factories' => [
            'agencyName' => 'Agent\Factory\View\Helper\AgentViewHelperFactory',
        ]
    ],
];