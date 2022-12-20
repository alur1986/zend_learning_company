<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Zend\Authentication\AuthenticationService' => 'Authentication\Factory\Service\AuthenticationServiceFactory',
            'Learner\LoggedIn' => 'Authentication\Factory\Service\LoggedInLearnerServiceFactory',
            'Authentication\Options' => 'Authentication\Factory\Service\OptionsServiceFactory',
        ],
        'aliases' => [
            'Learner' => 'Learner\LoggedIn'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Authentication\Controller\Authentication' => 'Authentication\Controller\AuthenticationController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Authentication\Form\Login' => 'Authentication\Factory\Form\LoginFormFactory'
        ]
    ],

    /**
     * VIEW HELPERS
     */
    'view_helpers' => [
        'invokables' => [
            'isLoggedIn' => 'Authentication\View\Helper\LoggedIn'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [
                'Authentication\EventManager\Listener\RouteListener',
                'route'
            ]
        ]
    ]
];