<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Group\Service' => 'Group\Factory\Service\GroupServiceFactory',
            'Group\ActiveGroups' => 'Group\Factory\Service\ActiveGroupsFactory',
            'Group\InActiveGroups' => 'Group\Factory\Service\InActiveGroupsFactory',
            'Group\All' => 'Group\Factory\Service\AllGroupsFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Group\Controller\Manage' => 'Group\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Group\Form\Create' => 'Group\Factory\Form\CreateGroupFormFactory',
            'Group\Form\Update' => 'Group\Factory\Form\UpdateGroupFormFactory'
        ],
        'initializers' => [
            'Group\Form\Initialiser'
        ]
    ],

    /**
     * LISTENER MANAGER
     */
    'listener_manager' => [
        'invokables' => [
            'Group\EventManager\InjectTemplateListener' => 'Group\EventManager\InjectTemplateListener'
        ]
    ]
];