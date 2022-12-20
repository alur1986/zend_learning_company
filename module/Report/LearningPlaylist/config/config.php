<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
        	'Report\LearningPlaylist\Service' => 'Report\LearningPlaylist\Factory\Service\ReportServiceFactory',
            'Report\LearningPlaylist\Templates' => 'Report\LearningPlaylist\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\LearningPlaylist\Form\Activities' => 'Report\LearningPlaylist\Factory\Form\ActivitiesFormFactory',
            'Report\LearningPlaylist\Form\Learners' => 'Report\LearningPlaylist\Factory\Form\LearnersFormFactory',

            'Report\LearningPlaylist\Form\Groups' => 'Report\LearningPlaylist\Factory\Form\GroupsFormFactory',

            'Report\LearningPlaylist\Form\Range' => 'Report\LearningPlaylist\Factory\Form\RangeFormFactory',

            'Report\LearningPlaylist\Form\FilterCreate' => 'Report\LearningPlaylist\Factory\Form\FilterCreateFormFactory',
            'Report\LearningPlaylist\Form\FilterUpdate' => 'Report\LearningPlaylist\Factory\Form\FilterUpdateFormFactory',
            'Report\LearningPlaylist\Form\FilterActivities' => 'Report\LearningPlaylist\Factory\Form\FilterActivitiesFormFactory',
            'Report\LearningPlaylist\Form\FilterGroups' => 'Report\LearningPlaylist\Factory\Form\FilterGroupsFormFactory',
            'Report\LearningPlaylist\Form\FilterLearners' => 'Report\LearningPlaylist\Factory\Form\FilterLearnersFormFactory',
            'Report\LearningPlaylist\Form\FilterRange' => 'Report\LearningPlaylist\Factory\Form\FilterRangeFormFactory',

            'Report\LearningPlaylist\Form\TemplateCreate' => 'Report\LearningPlaylist\Factory\Form\TemplateCreateFormFactory',
            'Report\LearningPlaylist\Form\TemplateUpdate' => 'Report\LearningPlaylist\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\LearningPlaylist\Controller\Manage' => 'Report\LearningPlaylist\Controller\ManageController',
            'Report\LearningPlaylist\Controller\Filter' => 'Report\LearningPlaylist\Controller\FilterController',
            'Report\LearningPlaylist\Controller\Template' => 'Report\LearningPlaylist\Controller\TemplateController'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\LearningPlaylist\EventManager\InjectTemplateListener' => 'Report\LearningPlaylist\EventManager\InjectTemplateListener'
        ]
    ]
];