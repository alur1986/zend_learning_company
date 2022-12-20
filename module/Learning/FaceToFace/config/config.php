<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'FaceToFace\Service' => 'FaceToFace\Factory\Service\FaceToFaceServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'FaceToFace\Controller\Manage' => 'FaceToFace\Controller\ManageController',
            'FaceToFace\Controller\Event' => 'FaceToFace\Controller\EventController',
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'FaceToFace\Form\Create' => 'FaceToFace\Factory\Form\CreateFaceToFaceFormFactory',
            'FaceToFace\Form\Update' => 'FaceToFace\Factory\Form\UpdateFaceToFaceFormFactory'
        ]
    ]
];