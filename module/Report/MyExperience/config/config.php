<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\MyExperience\Service' => 'Report\MyExperience\Factory\Service\ReportServiceFactory',
            'Report\MyExperience\Templates' => 'Report\MyExperience\Factory\Service\TemplatesServiceFactory',
            'Report\MyExperience\Filters' => 'Report\MyExperience\Factory\Service\FiltersServiceFactory',
        ],
        'delegators' => [
            'Learning\TincanActivities' => [
                'Report\MyExperience\Factory\Service\Delegator\FilterTincanActivitiesDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\MyExperience\Controller\Manage' => 'Report\MyExperience\Controller\ManageController',
            'Report\MyExperience\Controller\Report' => 'Report\MyExperience\Controller\ReportsController',
            //'Report\MyExperience\Controller\InspireReport' => 'Report\MyExperience\Controller\InspireReportsController',
            'Report\MyExperience\Controller\Template' => 'Report\MyExperience\Controller\TemplateController',
            'Report\MyExperience\Controller\Filter' => 'Report\MyExperience\Controller\FilterController',
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\MyExperience\Form\FilterCreate' => 'Report\MyExperience\Factory\Form\FilterCreateFormFactory',
            'Report\MyExperience\Form\FilterUpdate' => 'Report\MyExperience\Factory\Form\FilterUpdateFormFactory',
            'Report\MyExperience\Form\FilterActivities' => 'Report\MyExperience\Factory\Form\FilterActivitiesFormFactory',
            'Report\MyExperience\Form\FilterGroups' => 'Report\MyExperience\Factory\Form\FilterGroupsFormFactory',
            'Report\MyExperience\Form\FilterLearners' => 'Report\MyExperience\Factory\Form\FilterLearnersFormFactory',
            'Report\MyExperience\Form\FilterRange' => 'Report\MyExperience\Factory\Form\FilterRangeFormFactory',
            'Report\MyExperience\Form\TemplateCreate' => 'Report\MyExperience\Factory\Form\TemplateCreateFormFactory',
            'Report\MyExperience\Form\TemplateUpdate' => 'Report\MyExperience\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Report\MyExperience\EventManager\Listener\RouteListener', 'route' ],
            'priority' => -100
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\MyExperience\EventManager\InjectTemplateListener' => 'Report\MyExperience\EventManager\InjectTemplateListener'
        ]
    ]
];
