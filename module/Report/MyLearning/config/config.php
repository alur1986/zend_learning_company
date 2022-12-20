<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\MyLearning\Service' => 'Report\MyLearning\Factory\Service\ReportServiceFactory',
            'Report\MyLearning\Templates' => 'Report\MyLearning\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\MyLearning\Controller\Manage' => 'Report\MyLearning\Controller\ManageController',
            'Report\MyLearning\Controller\Filter' => 'Report\MyLearning\Controller\FilterController',
            'Report\MyLearning\Controller\Template' => 'Report\MyLearning\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\MyLearning\Form\Activities' => 'Report\MyLearning\Factory\Form\ActivitiesFormFactory',
            'Report\MyLearning\Form\Groups' => 'Report\MyLearning\Factory\Form\GroupsFormFactory',
            'Report\MyLearning\Form\Learners' => 'Report\MyLearning\Factory\Form\LearnersFormFactory',
            'Report\MyLearning\Form\Range' => 'Report\MyLearning\Factory\Form\RangeFormFactory',

            'Report\MyLearning\Form\FilterCreate' => 'Report\MyLearning\Factory\Form\FilterCreateFormFactory',
            'Report\MyLearning\Form\FilterUpdate' => 'Report\MyLearning\Factory\Form\FilterUpdateFormFactory',
            'Report\MyLearning\Form\FilterActivities' => 'Report\MyLearning\Factory\Form\FilterActivitiesFormFactory',
            'Report\MyLearning\Form\FilterGroups' => 'Report\MyLearning\Factory\Form\FilterGroupsFormFactory',
            'Report\MyLearning\Form\FilterLearners' => 'Report\MyLearning\Factory\Form\FilterLearnersFormFactory',
            'Report\MyLearning\Form\FilterRange' => 'Report\MyLearning\Factory\Form\FilterRangeFormFactory',

            'Report\MyLearning\Form\TemplateCreate' => 'Report\MyLearning\Factory\Form\TemplateCreateFormFactory',
            'Report\MyLearning\Form\TemplateUpdate' => 'Report\MyLearning\Factory\Form\TemplateUpdateFormFactory'
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\MyLearning\EventManager\InjectTemplateListener' => 'Report\MyLearning\EventManager\InjectTemplateListener'
        ]
    ]
];