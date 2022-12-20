<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Learner\Active' => 'Learner\Factory\Service\AllActiveLearnersFactory',
            'Learner\AllInactive' => 'Learner\Factory\Service\AllInActiveLearnersFactory',
            'Learner\All' => 'Learner\Factory\Service\AllLearnersFactory',
            'Learner\Options' => 'Learner\Factory\Service\OptionsServiceFactory',
            'Learner\Service' => 'Learner\Factory\Service\LearnerServiceFactory',
            'Learner\LearnerRole'=>'Learner\Factory\Service\LearnerRoleFactory'
        ],
        'aliases' => [
            'AllLearners' => 'Learner\All',
            'InActiveLearners' => 'Learner\AllInactive',
            'ActiveLearners' => 'Learner\Active'
        ],
        'delegators' => [
            'Learner\Options' => [
                'LazyServiceFactory'
            ]
        ]
    ],

    /**
     * LAZY SERVICES
     */
    'lazy_services' => [
        'class_map' => [
            'Learner\Options' => 'Learner\Service\Options'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Learner\Controller\Learner' => 'Learner\Controller\LearnerController',
            'Learner\Controller\Employment' => 'Learner\Controller\EmploymentController',
            'Learner\Controller\Settings' => 'Learner\Controller\SettingsController',
            'Learner\Controller\Photos' => 'Learner\Controller\PhotoController',
            'Learner\Controller\Distribution' => 'Learner\Controller\DistributionController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Learner\Form\Create' => 'Learner\Factory\Form\CreateFormFactory',
            'Learner\Form\Update' => 'Learner\Factory\Form\EditFormFactory',
            'Learner\Form\Register' => 'Learner\Factory\Form\RegisterFormFactory',
            'Learner\Form\Password' => 'Learner\Factory\Form\PasswordFormFactory',
            'Learner\Form\ForgotPassword' => 'Learner\Factory\Form\ForgotPasswordFormFactory',
            'Learner\Form\ResetPassword' => 'Learner\Factory\Form\ResetPasswordFormFactory',
            'Learner\Form\Employment' => 'Learner\Factory\Form\EmploymentFormFactory',
            'Learner\Form\Settings' => 'Learner\Factory\Form\SettingsFormFactory',
            'Learner\Form\Import' => 'Learner\Factory\Form\ImportFormFactory',
            'Learner\Form\Photo' => 'Learner\Factory\Form\PhotoFormFactory',
            'Learner\Form\Distribution' => 'Learner\Factory\Form\DistributionFormFactory'
        ],
        'initializers' => [
            'Learner\Form\Initialiser'
        ]
    ],

    /**
     * VIEW HELPERS
     */
    'view_helpers' => [
        'factories' => [
            'learner' => 'Learner\Factory\View\Helper\LearnerViewHelperFactory',
            'profile' => 'Learner\Factory\View\Helper\ProfileHelperFactory'
        ]
    ],

    /**
     * INPUT FILTERS
     */
    'input_filters' => [
        'invokables' => [
            'Learner\InputFilter\Learner' => 'Learner\InputFilter\Learner',
            'Learner\InputFilter\Employment' => 'Learner\InputFilter\Employment'
        ]
    ],

    /**
     * HYDRATORS
     */
    'hydrators' => [
        'invokables' => [
            'Learner\Hydrator\Learner' => 'Learner\Hydrator\AggregateHydrator'
        ],
        'factories' => [
            'Learner\Hydrator\Employment' => 'Learner\Hydrator\Employment'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Learner\EventManager\Listener\EventListener', 'postRoute' ],
            'priority' => -1
        ]
    ]
];