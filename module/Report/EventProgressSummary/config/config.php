<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\EventProgressSummary\Service' => 'Report\EventProgressSummary\Factory\Service\ReportServiceFactory',
            'Report\EventProgressSummary\Templates' => 'Report\EventProgressSummary\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => [
            'Learning\Activities' => [
                'Report\EventProgressSummary\Factory\Service\Delegator\FilterEventActivitiesDelegatorFactory'
            ],
            'Event\All' => [
                'Report\EventProgressSummary\Factory\Service\Delegator\FilterEventsByActivityIdDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\EventProgressSummary\Controller\Manage' => 'Report\EventProgressSummary\Controller\ManageController',
            'Report\EventProgressSummary\Controller\Filter' => 'Report\EventProgressSummary\Controller\FilterController',
            'Report\EventProgressSummary\Controller\Template' => 'Report\EventProgressSummary\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\EventProgressSummary\Form\Activities' => 'Report\EventProgressSummary\Factory\Form\ActivitiesFormFactory',
            'Report\EventProgressSummary\Form\Events' => 'Report\EventProgressSummary\Factory\Form\EventsFormFactory',
            'Report\EventProgressSummary\Form\Groups' => 'Report\EventProgressSummary\Factory\Form\GroupsFormFactory',
            'Report\EventProgressSummary\Form\Learners' => 'Report\EventProgressSummary\Factory\Form\LearnersFormFactory',
            'Report\EventProgressSummary\Form\Range' => 'Report\EventProgressSummary\Factory\Form\RangeFormFactory',

            'Report\EventProgressSummary\Form\FilterCreate' => 'Report\EventProgressSummary\Factory\Form\FilterCreateFormFactory',
            'Report\EventProgressSummary\Form\FilterUpdate' => 'Report\EventProgressSummary\Factory\Form\FilterUpdateFormFactory',
            'Report\EventProgressSummary\Form\FilterActivities' => 'Report\EventProgressSummary\Factory\Form\FilterActivitiesFormFactory',
            'Report\EventProgressSummary\Form\FilterEvents' => 'Report\EventProgressSummary\Factory\Form\FilterEventsFormFactory',
            'Report\EventProgressSummary\Form\FilterGroups' => 'Report\EventProgressSummary\Factory\Form\FilterGroupsFormFactory',
            'Report\EventProgressSummary\Form\FilterLearners' => 'Report\EventProgressSummary\Factory\Form\FilterLearnersFormFactory',
            'Report\EventProgressSummary\Form\FilterRange' => 'Report\EventProgressSummary\Factory\Form\FilterRangeFormFactory',

            'Report\EventProgressSummary\Form\TemplateCreate' => 'Report\EventProgressSummary\Factory\Form\TemplateCreateFormFactory',
            'Report\EventProgressSummary\Form\TemplateUpdate' => 'Report\EventProgressSummary\Factory\Form\TemplateUpdateFormFactory',
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\EventProgressSummary\EventManager\InjectTemplateListener' => 'Report\EventProgressSummary\EventManager\InjectTemplateListener'
        ]
    ]
];