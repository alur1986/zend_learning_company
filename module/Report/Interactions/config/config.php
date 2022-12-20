<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\Interactions\Service' => 'Report\Interactions\Factory\Service\ReportServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\Interactions\Controller\Manage' => 'Report\Interactions\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\Interactions\Form\IndexForm' => 'Report\Interactions\Factory\Form\IndexFormFactory',
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [
                'Report\Interactions\EventManager\Listener\RouteListener',
                'route'
            ]
        ],

        [
            'identifier' => 'Zend\View\Helper\Navigation\AbstractHelper',
            'event' => 'isAllowed',
            'callback' => [
                'Report\Interactions\EventManager\Listener\RouteListener',
                'navigation'
            ]
        ],

        [
            'identifier' => 'Zend\View\Renderer\PhpRenderer',
            'event' => 'render',
            'callback' => [
                'Report\Interactions\EventManager\Listener\RouteListener',
                'render'
            ]
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\Interactions\EventManager\InjectTemplateListener' => 'Report\Interactions\EventManager\InjectTemplateListener'
        ]
    ]
];
