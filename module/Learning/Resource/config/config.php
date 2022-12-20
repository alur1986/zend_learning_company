<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Resource\Service' => 'Resource\Factory\Service\ResourceServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Resource\Controller\Manage' => 'Resource\Controller\ManageController',
            'Resource\Controller\File' => 'Resource\Controller\FileController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Resource\Form\Create' => 'Resource\Factory\Form\CreateResourceFormFactory',
            'Resource\Form\Update' => 'Resource\Factory\Form\UpdateResourceFormFactory',
            'Resource\Form\FileUpload' => 'Resource\Factory\Form\FileUploadFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'identifier' => 'Learning\Service\LearningService',
            'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
            'callback' => 'Resource\EventManager\Event\Listener\DuplicateActivityListener'
        ]
    ]
];