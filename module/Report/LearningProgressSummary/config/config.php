<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
        	'Report\LearningProgressSummary\Service' => 'Report\LearningProgressSummary\Factory\Service\ReportServiceFactory',
        	'Report\LearningProgressSummary\TemplateService' => 'Report\LearningProgressSummary\Factory\Service\TemplateServiceServiceFactory',
        	'Report\LearningProgressSummary\Templates' => 'Report\LearningProgressSummary\Factory\Service\TemplatesServiceFactory',
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\LearningProgressSummary\Controller\Manage' => 'Report\LearningProgressSummary\Controller\ManageController',
            'Report\LearningProgressSummary\Controller\Filter' => 'Report\LearningProgressSummary\Controller\FilterController',
            'Report\LearningProgressSummary\Controller\Template' => 'Report\LearningProgressSummary\Controller\TemplateController',
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\LearningProgressSummary\Form\Activities' => 'Report\LearningProgressSummary\Factory\Form\ActivitiesFormFactory',
            'Report\LearningProgressSummary\Form\Groups' => 'Report\LearningProgressSummary\Factory\Form\GroupsFormFactory',
            'Report\LearningProgressSummary\Form\Learners' => 'Report\LearningProgressSummary\Factory\Form\LearnersFormFactory',
            'Report\LearningProgressSummary\Form\Range' => 'Report\LearningProgressSummary\Factory\Form\RangeFormFactory',

            'Report\LearningProgressSummary\Form\FilterCreate' => 'Report\LearningProgressSummary\Factory\Form\FilterCreateFormFactory',
            'Report\LearningProgressSummary\Form\FilterUpdate' => 'Report\LearningProgressSummary\Factory\Form\FilterUpdateFormFactory',
            'Report\LearningProgressSummary\Form\FilterActivities' => 'Report\LearningProgressSummary\Factory\Form\FilterActivitiesFormFactory',
            'Report\LearningProgressSummary\Form\FilterGroups' => 'Report\LearningProgressSummary\Factory\Form\FilterGroupsFormFactory',
            'Report\LearningProgressSummary\Form\FilterRange' => 'Report\LearningProgressSummary\Factory\Form\FilterRangeFormFactory',

            'Report\LearningProgressSummary\Form\TemplateCreate' => 'Report\LearningProgressSummary\Factory\Form\TemplateCreateFormFactory',
            'Report\LearningProgressSummary\Form\TemplateUpdate' => 'Report\LearningProgressSummary\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\LearningProgressSummary\EventManager\InjectTemplateListener' => 'Report\LearningProgressSummary\EventManager\InjectTemplateListener'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Report\LearningProgressSummary\EventManager\Listener\RouteListener', 'route' ],
            'priority' => -100
        ]
    ]
];