<?php

return [
    'router' => [
        'routes' => [
            'learner' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/learner',
                    'defaults' => [
                        'controller' => 'Learner\Controller\Learner',
                        'action' => 'directory'
                    ]
                ],
                'child_routes' => [
                    'directory' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/directory',
                            'constraints' => [
                                'page' => '[0-9]+'
                            ],
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

                    'register' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/register',
                            'defaults' => [
                                'action' => 'register'
                            ]
                        ]
                    ],

                    'permalink' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'action' => 'view'
                            ]
                        ]
                    ],

                    'read' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/:user_id',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
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
                            'route' => '/update[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{0,}'
                            ],
                            'defaults' => [
                                'action' => 'update'
                            ]
                        ]
                    ],

                    'password' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/password[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{0,}'
                            ],
                            'defaults' => [
                                'action' => 'password'
                            ]
                        ]
                    ],

                    'forgot-password' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/forgot-password',
                            'defaults' => [
                                'action' => 'forgot-password'
                            ]
                        ]
                    ],

                    'reset-password' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/reset-password[/:password_token]',
                            'constraints' => [
                                'password_token' => '[a-zA-Z0-9]{4,}'
                            ],
                            'defaults' => [
                                'controller' => 'Learner\Controller\Learner',
                                'action' => 'reset-password'
                            ]
                        ]
                    ],

                    'delete' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/delete/:user_id[/confirm/:confirm]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}',
                                'confirm' => '(yes|no)'
                            ],
                            'defaults' => [
                                'action' => 'delete'
                            ]
                        ]
                    ],

                    'activate' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/activate/:user_id[/confirm/:confirm]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}',
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
                            'route' => '/deactivate/:user_id[/confirm/:confirm]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}',
                                'confirm' => '(yes|no)'
                            ],
                            'defaults' => [
                                'action' => 'deactivate'
                            ]
                        ]
                    ],

                    'import' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/import[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'action' => 'import'
                            ]
                        ]
                    ],

                    'download' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/download',
                            'defaults' => [
                                'action' => 'csv'
                            ]
                        ],
                        'child_routes' => [
                            'csv' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/csv',
                                    'defaults' => [
                                        'action' => 'csv'
                                    ]
                                ]
                            ]
                        ]
                    ],

                    'employment' => [
                        'type' => 'Segment',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/employment[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'controller' => 'Learner\Controller\Employment',
                                'action' => 'manage'
                            ]
                        ]
                    ],

                    'photo' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/photo[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'controller' => 'Learner\Controller\Photos',
                                'action' => 'photo'
                            ]
                        ],
                        'child_routes' => [
                            'show' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/:filename',
                                    'constraints' => [
                                        'filename' => '[a-zA-Z0-9-]+\.(jpg|jpeg|gif|png|bmp)'
                                    ],
                                    'defaults' => [
                                        'action' => 'show'
                                    ]
                                ]
                            ],

                            'remove' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/remove[/confirm/:confirm]',
                                    'constraints' => [
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'remove'
                                    ]
                                ]
                            ]
                        ]
                    ],

                    'settings' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/settings[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'controller' => 'Learner\Controller\Settings',
                                'action' => 'settings'
                            ]
                        ],
                        'child_routes' => [
                            'generic' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '',
                                    'defaults' => [
                                        'action' => 'settings'
                                    ]
                                ]
                            ],

                            'email' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/email',
                                    'defaults' => [
                                        'action' => 'email'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'distribution' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/distribution[/:user_id]',
                            'defaults' => [
                                'controller' => 'Learner\Controller\Distribution',
                                'action' => 'directory'
                            ],
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ]
                        ]
                    ]
                ]
            ],

            'register' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/register',
                    'defaults' => [
                        'controller' => 'Learner\Controller\Learner',
                        'action' => 'register'
                    ]
                ]
            ],

            'forgot-password' => [
                'type' => 'Literal',
                'may_terminate' => false,
                'options' => [
                    'route' => '/forgot-password',
                    'defaults' => [
                        'controller' => 'Learner\Controller\Learner',
                        'action' => 'forgot-password'
                    ]
                ]
            ],

            'reset-password' => [
                'type' => 'Segment',
                'may_terminate' => false,
                'options' => [
                    'route' => '/reset-password[/:password_token]',
                    'constraints' => [
                        'password_token' => '[a-zA-Z0-9]{4,}'
                    ],
                    'defaults' => [
                        'controller' => 'Learner\Controller\Learner',
                        'action' => 'reset-password'
                    ]
                ]
            ]
        ]
    ]
];