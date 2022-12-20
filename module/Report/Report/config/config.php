<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\Options' => 'Report\Factory\Service\OptionsServiceFactory',
            'Report\Service' => 'Report\Factory\Service\ReportServiceFactory',
            'Report\TemplateService' => 'Report\Factory\Service\TemplateServiceFactory',
            'Report\FilterService' => 'Report\Factory\Service\FilterServiceFactory'
        ],
        'delegators' => [
            'Learner\All' => [
                'Report\Factory\Service\Delegator\FilterLearnersByGroupsDelegatorFactory'
            ],
            'Learning\AllActivities' => [
                'Report\Factory\Service\Delegator\FilterActivitiesDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\Controller\Manage' => 'Report\Controller\ManageController'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Report\EventManager\Listener\RouteListener', 'route' ],
            'priority' => -100
        ]
    ]
];