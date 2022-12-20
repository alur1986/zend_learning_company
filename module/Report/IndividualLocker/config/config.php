<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\IndividualLocker\Service' => 'Report\IndividualLocker\Factory\Service\ReportServiceFactory',
            'Report\IndividualLocker\Templates' => 'Report\IndividualLocker\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => [
            'Taxonomy\Categories' => [
                'Report\IndividualLocker\Factory\Service\Delegator\FilterMyLockerCategoriesDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\IndividualLocker\Controller\Manage' => 'Report\IndividualLocker\Controller\ManageController',
            'Report\IndividualLocker\Controller\Filter' => 'Report\IndividualLocker\Controller\FilterController',
            'Report\IndividualLocker\Controller\Template' => 'Report\IndividualLocker\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\IndividualLocker\Form\Categories' => 'Report\IndividualLocker\Factory\Form\CategoriesFormFactory',
            'Report\IndividualLocker\Form\Groups' => 'Report\IndividualLocker\Factory\Form\GroupsFormFactory',
            'Report\IndividualLocker\Form\Learners' => 'Report\IndividualLocker\Factory\Form\LearnersFormFactory',
            'Report\IndividualLocker\Form\Range' => 'Report\IndividualLocker\Factory\Form\RangeFormFactory',

            'Report\IndividualLocker\Form\FilterCreate' => 'Report\IndividualLocker\Factory\Form\FilterCreateFormFactory',
            'Report\IndividualLocker\Form\FilterUpdate' => 'Report\IndividualLocker\Factory\Form\FilterUpdateFormFactory',
            'Report\IndividualLocker\Form\FilterCategories' => 'Report\IndividualLocker\Factory\Form\FilterCategoriesFormFactory',
            'Report\IndividualLocker\Form\FilterGroups' => 'Report\IndividualLocker\Factory\Form\FilterGroupsFormFactory',
            'Report\IndividualLocker\Form\FilterLearners' => 'Report\IndividualLocker\Factory\Form\FilterLearnersFormFactory',
            'Report\IndividualLocker\Form\FilterRange' => 'Report\IndividualLocker\Factory\Form\FilterRangeFormFactory',

            'Report\IndividualLocker\Form\TemplateCreate' => 'Report\IndividualLocker\Factory\Form\TemplateCreateFormFactory',
            'Report\IndividualLocker\Form\TemplateUpdate' => 'Report\IndividualLocker\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Report\IndividualLocker\EventManager\Listener\RouteListener', 'route' ],
            'priority' => -100
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\IndividualLocker\EventManager\InjectTemplateListener' => 'Report\IndividualLocker\EventManager\InjectTemplateListener'
        ]
    ]
];