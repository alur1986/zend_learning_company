<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\IndividualLearning\Service' => 'Report\IndividualLearning\Factory\Service\ReportServiceFactory',
            'Report\IndividualLearning\Templates' => 'Report\IndividualLearning\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\IndividualLearning\Controller\Manage' => 'Report\IndividualLearning\Controller\ManageController',
            'Report\IndividualLearning\Controller\Filter' => 'Report\IndividualLearning\Controller\FilterController',
            'Report\IndividualLearning\Controller\Template' => 'Report\IndividualLearning\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\IndividualLearning\Form\Activities' => 'Report\IndividualLearning\Factory\Form\ActivitiesFormFactory',
            'Report\IndividualLearning\Form\Groups' => 'Report\IndividualLearning\Factory\Form\GroupsFormFactory',
            'Report\IndividualLearning\Form\Learners' => 'Report\IndividualLearning\Factory\Form\LearnersFormFactory',
            'Report\IndividualLearning\Form\Range' => 'Report\IndividualLearning\Factory\Form\RangeFormFactory',

            'Report\IndividualLearning\Form\FilterCreate' => 'Report\IndividualLearning\Factory\Form\FilterCreateFormFactory',
            'Report\IndividualLearning\Form\FilterUpdate' => 'Report\IndividualLearning\Factory\Form\FilterUpdateFormFactory',
            'Report\IndividualLearning\Form\FilterActivities' => 'Report\IndividualLearning\Factory\Form\FilterActivitiesFormFactory',
            'Report\IndividualLearning\Form\FilterGroups' => 'Report\IndividualLearning\Factory\Form\FilterGroupsFormFactory',
            'Report\IndividualLearning\Form\FilterLearners' => 'Report\IndividualLearning\Factory\Form\FilterLearnersFormFactory',
            'Report\IndividualLearning\Form\FilterRange' => 'Report\IndividualLearning\Factory\Form\FilterRangeFormFactory',

            'Report\IndividualLearning\Form\TemplateCreate' => 'Report\IndividualLearning\Factory\Form\TemplateCreateFormFactory',
            'Report\IndividualLearning\Form\TemplateUpdate' => 'Report\IndividualLearning\Factory\Form\TemplateUpdateFormFactory'
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\IndividualLearning\EventManager\InjectTemplateListener' => 'Report\IndividualLearning\EventManager\InjectTemplateListener'
        ]
    ]
];