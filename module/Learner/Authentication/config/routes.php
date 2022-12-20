<?php

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => 'Segment',
                'priority' => 9999,
                'may_terminate' => true,
                'options' => [
                    'route' => '/login[/:identity]',
                    'scheme' => 'https',
                    'constraints' => [
                        'identity' => '[a-zA-Z0-9@.-]*'
                    ],
                    'defaults' => [
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'login'
                    ]
                ]
            ],

            'ssologin' => [
                'type' => 'Segment',
                'priority' => 9999,
                'options' => [
                    'route' => '/sso/:jwt_token',
                    'scheme' => 'https',
                    'constraints' => [
                        'jwt_token' => '[a-zA-Z0-9-._]+'
                    ],
                    'defaults' => [
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'sso'
                    ]
                ]
            ],

            'logout' => [
                'type' => 'Literal',
                'priority' => 9999,
                'may_terminate' => true,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => 'Authentication\Controller\Authentication',
                        'action' => 'logout'
                    ]
                ]
            ],

            'learner' => [
                'child_routes' => [
                    'login' => [
                        'type' => 'Segment',
                        'priority' => 9999,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/login[/:identity]',
                            'scheme' => 'https',
                            'constraints' => [
                                'identity' => '[a-zA-Z0-9@.-]*'
                            ],
                            'defaults' => [
                                'controller' => 'Authentication\Controller\Authentication',
                                'action' => 'login'
                            ]
                        ]
                    ],

                    'logout' => [
                        'type' => 'Literal',
                        'priority' => 9999,
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => 'Authentication\Controller\Authentication',
                                'action' => 'logout'
                            ]
                        ]
                    ],

                    'impersonate' => [
                        'type' => 'Segment',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/impersonate/:authentication_token',
                            'constraints' => [
                                'authentication_token' => '[a-zA-Z0-9]+'
                            ],
                            'defaults' => [
                                'controller' => 'Authentication\Controller\Authentication',
                                'action' => 'impersonate'
                            ]
                        ]
                    ],
                    'autologin' => [
                        'type' => 'Segment',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/autologin[/:authentication_token]',
                            'constraints' => [
                                'user_id' => '[0-9]{4,}',
                                'authentication_token' => '[a-zA-Z0-9]+'
                            ],
                            'defaults' => [
                                'controller' => 'Authentication\Controller\Authentication',
                                'action' => 'impersonate'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
