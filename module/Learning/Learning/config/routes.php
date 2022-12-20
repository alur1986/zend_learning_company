<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/learning',
                    'defaults' => [
                        'controller' => 'Learning\Controller\Manage',
                        'action' => 'directory'
                    ]
                ],
                'child_routes' => [
                    'manage' => [
                        'type' => 'Literal',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/manage',
                            'defaults' => [
                                'action' => 'tools'
                            ]
                        ],
                        'child_routes' => [
                            'tools' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/tools',
                                    'defaults' => [
                                        'action' => 'tools'
                                    ]
                                ]
                            ],

                            'directory' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory',
                                    'defaults' => [
                                        'action' => 'directory'
                                    ]
                                ],
                                'child_routes' => [
                                    'learning' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/learning',
                                            'defaults' => [
                                                'action' => 'directory',
                                                'group_type' => 'learning'
                                            ]
                                        ]
                                    ],

                                    'event' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/event',
                                            'defaults' => [
                                                'action' => 'directory',
                                                'group_type' => 'event'
                                            ]
                                        ]
                                    ],

                                    'assessment' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/assessment',
                                            'defaults' => [
                                                'action' => 'directory',
                                                'group_type' => 'assessment'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];