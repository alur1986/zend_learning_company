<?php

return [
    'router' => [
        'routes' => [
            'notification' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/notification',
                    'defaults' => [
                        'controller' => 'Notification\Controller\Learner',
                        'action' => 'directory'
                    ]
                ],
                'child_routes' => [
                    'directory' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/directory',
                            'defaults' => [
                                'action' => 'directory'
                            ]
                        ]
                    ],

                    'read' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/:notification_id',
                            'constraints' => [
                                'notification_id' => '[0-9]+'
                            ],
                            'defaults' => [
                                'action' => 'read'
                            ]
                        ]
                    ],

                    'manage' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/manage',
                            'defaults' => [
                                'controller' => 'Notification\Controller\Manage',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory',
                                    'defaults' => [
                                        'action' => 'directory'
                                    ]
                                ]
                            ],

                            'create' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/create',
                                    'defaults' => [
                                        'action' => 'create'
                                    ]
                                ]
                            ],

                            'read' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/:notification_id',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'read'
                                    ]
                                ]
                            ],

                            'update' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/update/:notification_id',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ]
                            ],

                            'activate' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/activate/:notification_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'activate'
                                    ]
                                ]
                            ],

                            'deactivate' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/deactivate/:notification_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ],

                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete/:notification_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'delete'
                                    ]
                                ]
                            ]
                        ]
                    ],

                    'learner' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/learner',
                            'defaults' => [
                                'controller' => 'Notification\Controller\Learner',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'send' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/send/:notification_id',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'send'
                                    ]
                                ]
                            ]
                        ]
                    ],

                    'group' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/group',
                            'defaults' => [
                                'controller' => 'Notification\Controller\Group',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'send' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/send/:notification_id',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'send'
                                    ]
                                ]
                            ]
                        ]
                    ],

                    'site' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/site',
                            'defaults' => [
                                'controller' => 'Notification\Controller\Site',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'send' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/send/:notification_id',
                                    'constraints' => [
                                        'notification_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'send'
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