<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Scorm12\Service' => 'Scorm12\Factory\Service\Scorm12ServiceFactory',
            'Scorm12\Options' => 'Scorm12\Factory\Service\OptionsServiceFactory',
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Scorm12\Controller\Manage' => 'Scorm12\Controller\ManageController',
            'Scorm12\Controller\File' => 'Scorm12\Controller\FileController',
            'Scorm12\Controller\Item' => 'Scorm12\Controller\ItemController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Scorm12\Form\Create' => 'Scorm12\Factory\Form\CreateScorm12FormFactory',
            'Scorm12\Form\Update' => 'Scorm12\Factory\Form\UpdateScorm12FormFactory',
            'Scorm12\Form\FileUpload' => 'Scorm12\Factory\Form\FileUploadFormFactory',
            'Scorm12\Form\ItemUpdate' => 'Scorm12\Factory\Form\UpdateItemFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Scorm12\EventManager\Event\Listener\RouteListener', 'route' ]
        ],
        [
        	'identifier' => 'Learning\Service\LearningService',
        	'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
        	'callback' => 'Scorm12\EventManager\Event\Listener\DuplicateActivityListener'
        ]
    ]
];