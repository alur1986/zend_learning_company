<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Learning\Service'              => 'Learning\Factory\Service\LearningServiceFactory',
            'Learning\Options'              => 'Learning\Factory\Service\OptionsServiceFactory',
            'Learning\ActivityTypes'        => 'Learning\Factory\Service\ActivityTypesServiceFactory',
            'Learning\EventTypes'           => 'Learning\Factory\Service\EventTypesServiceFactory',
            'Learning\AssessmentTypes'      => 'Learning\Factory\Service\AssessmentTypesServiceFactory',
            'Learning\LearningTypes'        => 'Learning\Factory\Service\LearningTypesServiceFactory',
            'Learning\AllActivities'        => 'Learning\Factory\Service\AllLearningActivitiesServiceFactory',
            'Learning\ActiveActivities'     => 'Learning\Factory\Service\ActiveLearningActivitiesServiceFactory',
            'Learning\Activities'           => 'Learning\Factory\Service\LearningActivitiesServiceFactory',
            'Learning\LearningActivities'   => 'Learning\Factory\Service\LearningTypeActivitiesServiceFactory',
            'Learning\EventActivities'      => 'Learning\Factory\Service\EventActivitiesServiceFactory',
            'Learning\AssessmentActivities' => 'Learning\Factory\Service\AssessmentActivitiesServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Learning\Controller\Manage' => 'Learning\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENT MANAGER
     */
    'form_elements' => [
        'factories' => [
            'Learning\Form\Create' => 'Learning\Factory\Form\CreateFormFactory',
            'Learning\Form\Update' => 'Learning\Factory\Form\UpdateFormFactory'
        ],
        'initializers' => [
            'Learning\Form\Initialiser'
        ]
    ],

    /**
     * VIEW HELPERS
     */
    'view_helpers' => [
        'factories' => [
            'activityType' => 'Learning\Factory\View\Helper\ActivityTypeHelperFactory',
            'licenseCount' => 'Learning\Factory\View\Helper\LicenseCountHelperFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'identifier' => 'Learning\Service\LearningService',
            'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
            'callback' => 'Learning\EventManager\Listener\DuplicateActivityListener',
            'priority' => -100
        ]
    ]
];