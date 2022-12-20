<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Notification\Service' => 'Notification\Factory\Service\NotificationServiceFactory',

            'Notifications\All' => 'Notification\Factory\Service\NotificationsAllServiceFactory',
            'Notifications\Active' => 'Notification\Factory\Service\NotificationsActiveServiceFactory',
            'Notifications\Learner' => 'Notification\Factory\Service\NotificationsLearnerServiceFactory',
            'Notifications\Group' => 'Notification\Factory\Service\NotificationsGroupServiceFactory',
            'Notifications\Site' => 'Notification\Factory\Service\NotificationsSiteServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Notification\Controller\Manage' => 'Notification\Controller\ManageController',
            'Notification\Controller\Learner' => 'Notification\Controller\LearnerController',
            'Notification\Controller\Group' => 'Notification\Controller\GroupController',
            'Notification\Controller\Site' => 'Notification\Controller\SiteController',
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'listener_manager' => [
        'invokables' => []
    ],

    /**
     * FORM ELEMENT MANAGER
     */
    'form_elements' => [
        'factories' => [
            'Notification\Form\Create' => 'Notification\Factory\Form\CreateFormFactory',
            'Notification\Form\Update' => 'Notification\Factory\Form\UpdateFormFactory',
            'Notification\Form\Learner' => 'Notification\Factory\Form\LearnerFormFactory',
            'Notification\Form\Group' => 'Notification\Factory\Form\GroupFormFactory',
            'Notification\Form\Site' => 'Notification\Factory\Form\SiteFormFactory',
        ]
    ],

    /**
     * INPUT FILTERS
     */
    'input_filters' => [
        'invokables' => [
            'Notification\InputFilter\Notification' => 'Notification\InputFilter\Notification'
        ]
    ],

    /**
     * HYDRATORS
     */
    'hydrators' => [
        'invokables' => [
            'Notification\Hydrator\Notification' => 'Notification\Hydrator\AggregateHydrator'
        ]
    ]
];