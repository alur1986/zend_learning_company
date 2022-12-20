<?php

return [
    'authorization' => [
        'options' => [
            'enabled' => true,
            'default_permission' => 'deny',
            'default_role' => 'guest',
            'default_logged_in_role' => 'learner',
        ],

        /**
         * GUARD MANAGER
         */
        'guard_manager' => [
            'factories' => [
                'Authorization\Guard\Route' => 'Authorization\Factory\Guard\RouteGuardFactory',
                'Authorization\Guard\ViewModel' => 'Authorization\Factory\Guard\ViewModelGuardFactory',
                'Authorization\Guard\Uri' => 'Authorization\Factory\Guard\UriGuardFactory',
            ]
        ]
    ]
];