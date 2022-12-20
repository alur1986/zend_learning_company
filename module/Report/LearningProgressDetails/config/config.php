<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
        	'Report\LearningProgressDetails\Service' => 'Report\LearningProgressDetails\Factory\Service\ReportServiceFactory',
            'Report\LearningProgressDetails\Templates' => 'Report\LearningProgressDetails\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\LearningProgressDetails\Controller\Manage' => 'Report\LearningProgressDetails\Controller\ManageController',
            'Report\LearningProgressDetails\Controller\Filter' => 'Report\LearningProgressDetails\Controller\FilterController',
            'Report\LearningProgressDetails\Controller\Template' => 'Report\LearningProgressDetails\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\LearningProgressDetails\Form\Activities' => 'Report\LearningProgressDetails\Factory\Form\ActivitiesFormFactory',
            'Report\LearningProgressDetails\Form\Groups' => 'Report\LearningProgressDetails\Factory\Form\GroupsFormFactory',
            'Report\LearningProgressDetails\Form\Learners' => 'Report\LearningProgressDetails\Factory\Form\LearnersFormFactory',
            'Report\LearningProgressDetails\Form\Range' => 'Report\LearningProgressDetails\Factory\Form\RangeFormFactory',

            'Report\LearningProgressDetails\Form\FilterCreate' => 'Report\LearningProgressDetails\Factory\Form\FilterCreateFormFactory',
            'Report\LearningProgressDetails\Form\FilterUpdate' => 'Report\LearningProgressDetails\Factory\Form\FilterUpdateFormFactory',
            'Report\LearningProgressDetails\Form\FilterActivities' => 'Report\LearningProgressDetails\Factory\Form\FilterActivitiesFormFactory',
            'Report\LearningProgressDetails\Form\FilterGroups' => 'Report\LearningProgressDetails\Factory\Form\FilterGroupsFormFactory',
            'Report\LearningProgressDetails\Form\FilterLearners' => 'Report\LearningProgressDetails\Factory\Form\FilterLearnersFormFactory',
            'Report\LearningProgressDetails\Form\FilterRange' => 'Report\LearningProgressDetails\Factory\Form\FilterRangeFormFactory',

            'Report\LearningProgressDetails\Form\TemplateCreate' => 'Report\LearningProgressDetails\Factory\Form\TemplateCreateFormFactory',
            'Report\LearningProgressDetails\Form\TemplateUpdate' => 'Report\LearningProgressDetails\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\LearningProgressDetails\EventManager\InjectTemplateListener' => 'Report\LearningProgressDetails\EventManager\InjectTemplateListener'
        ]
    ]
];