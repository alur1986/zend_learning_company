<?php

return [
    'router' => [
        'routes' => [
            'group' => [
                'type' => 'Segment',
                'may_terminate' => false,
                'options' => [
                    'route' => '/group',
                    'defaults' => [
                        'controller' => 'Group\Controller\Manage',
                        'action' => 'directory'
                    ]
                ],
                'child_routes' => [
                    'directory' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[directory]',
                            'constraints' => [
                                'page' => '[0-9]+'
                            ],
                            'defaults' => [
                                'action' => 'directory'
                            ]
                        ]
                    ],

                    'create' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/create',
                            'defaults' => [
                                'action' => 'create'
                            ]
                        ]
                    ],

                    'read' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/:group_id',
                            'constraints' => [
                                'group_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'action' => 'read'
                            ]
                        ]
                    ],

                    'update' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/update/:group_id',
                            'constraints' => [
                                'group_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'action' => 'update'
                            ]
                        ]
                    ],

                    'delete' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/delete/:group_id[/confirm/:confirm]',
                            'constraints' => [
                                'group_id' => '[0-9]{1,}',
                                'confirm' => '(yes|no)'
                            ],
                            'defaults' => [
                                'action' => 'delete'
                            ]
                        ]
                    ],

                    'activate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/activate/:group_id[/confirm/:confirm]',
                            'constraints' => [
                                'group_id' => '[0-9]{1,}',
                                'confirm' => '(yes|no)'
                            ],
                            'defaults' => [
                                'action' => 'activate'
                            ]
                        ]
                    ],

                    'deactivate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/deactivate/:group_id[/confirm/:confirm]',
                            'constraints' => [
                                'group_id' => '[0-9]{1,}',
                                'confirm' => '(yes|no)'
                            ],
                            'defaults' => [
                                'action' => 'deactivate'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];