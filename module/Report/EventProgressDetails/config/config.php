<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\EventProgressDetails\Service' => 'Report\EventProgressDetails\Factory\Service\ReportServiceFactory',
            'Report\EventProgressDetails\Templates' => 'Report\EventProgressDetails\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => [
            'Learning\Activities' => [
                'Report\EventProgressDetails\Factory\Service\Delegator\FilterEventActivitiesDelegatorFactory'
            ],
            'Event\All' => [
                'Report\EventProgressDetails\Factory\Service\Delegator\FilterEventsByActivityIdDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\EventProgressDetails\Controller\Manage' => 'Report\EventProgressDetails\Controller\ManageController',
            'Report\EventProgressDetails\Controller\Filter' => 'Report\EventProgressDetails\Controller\FilterController',
            'Report\EventProgressDetails\Controller\Template' => 'Report\EventProgressDetails\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\EventProgressDetails\Form\Activities' => 'Report\EventProgressDetails\Factory\Form\ActivitiesFormFactory',
            'Report\EventProgressDetails\Form\Events' => 'Report\EventProgressDetails\Factory\Form\EventsFormFactory',
            'Report\EventProgressDetails\Form\Groups' => 'Report\EventProgressDetails\Factory\Form\GroupsFormFactory',
            'Report\EventProgressDetails\Form\Learners' => 'Report\EventProgressDetails\Factory\Form\LearnersFormFactory',
            'Report\EventProgressDetails\Form\Range' => 'Report\EventProgressDetails\Factory\Form\RangeFormFactory',

            'Report\EventProgressDetails\Form\FilterCreate' => 'Report\EventProgressDetails\Factory\Form\FilterCreateFormFactory',
            'Report\EventProgressDetails\Form\FilterUpdate' => 'Report\EventProgressDetails\Factory\Form\FilterUpdateFormFactory',
            'Report\EventProgressDetails\Form\FilterActivities' => 'Report\EventProgressDetails\Factory\Form\FilterActivitiesFormFactory',
            'Report\EventProgressDetails\Form\FilterEvents' => 'Report\EventProgressDetails\Factory\Form\FilterEventsFormFactory',
            'Report\EventProgressDetails\Form\FilterGroups' => 'Report\EventProgressDetails\Factory\Form\FilterGroupsFormFactory',
            'Report\EventProgressDetails\Form\FilterLearners' => 'Report\EventProgressDetails\Factory\Form\FilterLearnersFormFactory',
            'Report\EventProgressDetails\Form\FilterRange' => 'Report\EventProgressDetails\Factory\Form\FilterRangeFormFactory',

            'Report\EventProgressDetails\Form\TemplateCreate' => 'Report\EventProgressDetails\Factory\Form\TemplateCreateFormFactory',
            'Report\EventProgressDetails\Form\TemplateUpdate' => 'Report\EventProgressDetails\Factory\Form\TemplateUpdateFormFactory'
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\EventProgressDetails\EventManager\InjectTemplateListener' => 'Report\EventProgressDetails\EventManager\InjectTemplateListener'
        ]
    ]
];