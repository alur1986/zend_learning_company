<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Zend\Authorization\AuthorizationService' => 'Authorization\Factory\Service\AuthorizationServiceFactory',
            'Authorization\Options' => 'Authorization\Factory\Service\OptionsServiceFactory',

            'Authorization\Roles\All' => 'Authorization\Factory\Service\Role\AllRolesServiceFactory',
            'Authorization\Role\One' => 'Authorization\Factory\Service\Role\OneRoleServiceFactory',
            'Authorization\Role\Learners' => 'Authorization\Factory\Service\Role\LearnersServiceFactory',
            'Authorization\Role\Rules' => 'Authorization\Factory\Service\Role\RulesServiceFactory',
            'Authorization\Resources\All' => 'Authorization\Factory\Service\Resource\AllResourcesServiceFactory',
            'Authorization\Level\All' => 'Authorization\Factory\Service\Level\AllLevelsServiceFactory',

            'Authorization\Guards' => 'Authorization\Factory\Guard\GuardsServiceProviderFactory',
            'Authorization\Guard\GuardProviderPluginManager' => 'Authorization\Guard\GuardManager\GuardProviderPluginManagerFactory'
        ],
        'delegators' => [
            'Authorization\Resources\All' => [
                'Authorization\Factory\Service\Resource\Delegator\FilterByLevelResourceServiceDelegatorFactory'
            ],
            'Authorization\Roles\All' => [
                'Authorization\Factory\Service\Role\Delegator\FilterByLevelRoleServiceDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Authorization\Controller\Role' => 'Authorization\Controller\RoleController',
            'Authorization\Controller\Resource' => 'Authorization\Controller\ResourceController',
            'Authorization\Controller\Rule' => 'Authorization\Controller\RuleController',
            'Authorization\Controller\Learner' => 'Authorization\Controller\LearnerController',
            'Authorization\Controller\Manage' => 'Authorization\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Authorization\Role\Form\Create' => 'Authorization\Factory\Form\Role\CreateFormFactory',
            'Authorization\Role\Form\Update' => 'Authorization\Factory\Form\Role\UpdateFormFactory',

            'Authorization\Resource\Form\Create' => 'Authorization\Factory\Form\Resource\CreateFormFactory',
            'Authorization\Resource\Form\Update' => 'Authorization\Factory\Form\Resource\UpdateFormFactory',

            'Authorization\Rule\Form\Create' => 'Authorization\Factory\Form\Rule\CreateFormFactory',
            'Authorization\Rule\Form\Update' => 'Authorization\Factory\Form\Rule\UpdateFormFactory',

            'Authorization\Learner\Form\Learner' => 'Authorization\Factory\Form\Learner\LearnerFormFactory'
        ],
        'initializers' => [
            'Authorization\Form\Role\Initialiser',
            'Authorization\Form\Resource\Initialiser',
            'Authorization\Form\Level\Initialiser'
        ]
    ],

    /**
     * VIEW HELPERS
     */
    'view_helpers' => [
        'factories' => [
            'isGranted' => 'Authorization\Factory\View\Helper\IsGrantedHelperFactory',
            'isAllowed' => 'Authorization\Factory\View\Helper\IsGrantedHelperFactory',
            'role' => 'Authorization\Factory\View\Helper\RoleHelperFactory'
        ]
    ],

    /**
     * CONTROLLER PLUGINS
     */
    'controller_plugins' => [
        'factories' => [
            'isGranted' => 'Authorization\Factory\Mvc\Controller\Plugin\IsGrantedPluginFactory',
            'isAllowed' => 'Authorization\Factory\Mvc\Controller\Plugin\IsGrantedPluginFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => ['Authorization\EventManager\Listener\RouteListener', 'routeListener'],
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Authorization\EventManager\InjectTemplateListener' => 'Authorization\EventManager\InjectTemplateListener'
        ]
    ]/*,

    'view_manager' => [
        'display_exceptions'       => true,
        'exception_template'       => 'login'
    ]*/
];