<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'LearningPlan\Service' => 'LearningPlan\Factory\Service\LearningPlanServiceFactory',
            'LearningPlan\Options' => 'LearningPlan\Factory\Service\OptionsServiceFactory',
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'LearningPlan\Controller\Manage' => 'LearningPlan\Controller\ManageController',
            'LearningPlan\Controller\Activities' => 'LearningPlan\Controller\ActivitiesController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'LearningPlan\Form\Create'     => 'LearningPlan\Factory\Form\CreateFormFactory',
            'LearningPlan\Form\Update'     => 'LearningPlan\Factory\Form\UpdateFormFactory',
            'LearningPlan\Form\Activities' => 'LearningPlan\Factory\Form\ActivitiesFormFactory',
        ],
        'delegators' => []
    ]
];