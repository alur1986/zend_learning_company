<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\MyLocker\Service' => 'Report\MyLocker\Factory\Service\ReportServiceFactory',
            'Report\MyLocker\Templates' => 'Report\MyLocker\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => [
            'Taxonomy\Categories' => [
                'Report\MyLocker\Factory\Service\Delegator\FilterMyLockerCategoriesDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\MyLocker\Controller\Manage' => 'Report\MyLocker\Controller\ManageController',
            'Report\MyLocker\Controller\Filter' => 'Report\MyLocker\Controller\FilterController',
            'Report\MyLocker\Controller\Template' => 'Report\MyLocker\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\MyLocker\Form\Categories' => 'Report\MyLocker\Factory\Form\CategoriesFormFactory',
            'Report\MyLocker\Form\Groups' => 'Report\MyLocker\Factory\Form\GroupsFormFactory',
            'Report\MyLocker\Form\Learners' => 'Report\MyLocker\Factory\Form\LearnersFormFactory',
            'Report\MyLocker\Form\Range' => 'Report\MyLocker\Factory\Form\RangeFormFactory',

            'Report\MyLocker\Form\FilterCreate' => 'Report\MyLocker\Factory\Form\FilterCreateFormFactory',
            'Report\MyLocker\Form\FilterUpdate' => 'Report\MyLocker\Factory\Form\FilterUpdateFormFactory',
            'Report\MyLocker\Form\FilterCategories' => 'Report\MyLocker\Factory\Form\FilterCategoriesFormFactory',
            'Report\MyLocker\Form\FilterGroups' => 'Report\MyLocker\Factory\Form\FilterGroupsFormFactory',
            'Report\MyLocker\Form\FilterLearners' => 'Report\MyLocker\Factory\Form\FilterLearnersFormFactory',
            'Report\MyLocker\Form\FilterRange' => 'Report\MyLocker\Factory\Form\FilterRangeFormFactory',

            'Report\MyLocker\Form\TemplateCreate' => 'Report\MyLocker\Factory\Form\TemplateCreateFormFactory',
            'Report\MyLocker\Form\TemplateUpdate' => 'Report\MyLocker\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => 'Report\MyLocker\EventManager\Listener\RouteListener',
            'priority' => -100
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\MyLocker\EventManager\InjectTemplateListener' => 'Report\MyLocker\EventManager\InjectTemplateListener'
        ]
    ]
];