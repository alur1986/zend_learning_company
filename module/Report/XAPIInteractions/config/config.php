<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
        	'Report\XAPIInteractions\Service' => 'Report\XAPIInteractions\Factory\Service\ReportServiceFactory',
            'Report\XAPIInteractions\Templates' => 'Report\XAPIInteractions\Factory\Service\TemplatesServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\XAPIInteractions\Controller\Manage' => 'Report\XAPIInteractions\Controller\ManageController',
            'Report\XAPIInteractions\Controller\Filter' => 'Report\XAPIInteractions\Controller\FilterController',
            'Report\XAPIInteractions\Controller\Template' => 'Report\XAPIInteractions\Controller\TemplateController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Report\XAPIInteractions\Form\Activities' => 'Report\XAPIInteractions\Factory\Form\ActivitiesFormFactory',
            'Report\XAPIInteractions\Form\Groups' => 'Report\XAPIInteractions\Factory\Form\GroupsFormFactory',
            'Report\XAPIInteractions\Form\Learners' => 'Report\XAPIInteractions\Factory\Form\LearnersFormFactory',
            'Report\XAPIInteractions\Form\Range' => 'Report\XAPIInteractions\Factory\Form\RangeFormFactory',

            'Report\XAPIInteractions\Form\FilterCreate' => 'Report\XAPIInteractions\Factory\Form\FilterCreateFormFactory',
            'Report\XAPIInteractions\Form\FilterUpdate' => 'Report\XAPIInteractions\Factory\Form\FilterUpdateFormFactory',
            'Report\XAPIInteractions\Form\FilterActivities' => 'Report\XAPIInteractions\Factory\Form\FilterActivitiesFormFactory',
            'Report\XAPIInteractions\Form\FilterGroups' => 'Report\XAPIInteractions\Factory\Form\FilterGroupsFormFactory',
            'Report\XAPIInteractions\Form\FilterLearners' => 'Report\XAPIInteractions\Factory\Form\FilterLearnersFormFactory',
            'Report\XAPIInteractions\Form\FilterRange' => 'Report\XAPIInteractions\Factory\Form\FilterRangeFormFactory',

            'Report\XAPIInteractions\Form\TemplateCreate' => 'Report\XAPIInteractions\Factory\Form\TemplateCreateFormFactory',
            'Report\XAPIInteractions\Form\TemplateUpdate' => 'Report\XAPIInteractions\Factory\Form\TemplateUpdateFormFactory',
        ],
        'delegators' => []
    ],

    /**
     * EVENT LISTENERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\XAPIInteractions\EventManager\InjectTemplateListener' => 'Report\XAPIInteractions\EventManager\InjectTemplateListener'
        ]
    ],

    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [
                'Report\XAPIInteractions\EventManager\Listener\RouteListener',
                'route'
            ]
        ],

        [
            'identifier' => 'Zend\View\Helper\Navigation\AbstractHelper',
            'event' => 'isAllowed',
            'callback' => [
                'Report\XAPIInteractions\EventManager\Listener\RouteListener',
                'navigation'
            ]
        ],

        [
            'identifier' => 'Zend\View\Renderer\PhpRenderer',
            'event' => 'render',
            'callback' => [
                'Report\XAPIInteractions\EventManager\Listener\RouteListener',
                'render'
            ]
        ]
    ],
];
