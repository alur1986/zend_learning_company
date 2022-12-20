<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Tincan\Service' => 'Tincan\Factory\Service\TincanServiceFactory',
            'Tincan\Options' => 'Tincan\Factory\Service\OptionsServiceFactory',
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Tincan\Controller\Manage' => 'Tincan\Controller\ManageController',
            'Tincan\Controller\File' => 'Tincan\Controller\FileController',
            'Tincan\Controller\Item' => 'Tincan\Controller\ItemController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Tincan\Form\Create' => 'Tincan\Factory\Form\CreateTincanFormFactory',
            'Tincan\Form\Update' => 'Tincan\Factory\Form\UpdateTincanFormFactory',
            'Tincan\Form\FileUpload' => 'Tincan\Factory\Form\FileUploadFormFactory',
            'Tincan\Form\ItemUpdate' => 'Tincan\Factory\Form\UpdateItemFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'event' => \Zend\Mvc\MvcEvent::EVENT_ROUTE,
            'callback' => [ 'Tincan\EventManager\Event\Listener\RouteListener', 'route' ]
        ],
        [
        	'identifier' => 'Learning\Service\LearningService',
        	'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
        	'callback' => 'Tincan\EventManager\Event\Listener\DuplicateActivityListener'
        ]
    ]
];