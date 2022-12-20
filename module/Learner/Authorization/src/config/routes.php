<?php
return [
    'router' => [
        'routes' => [
            'secure' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/secure',
                    'defaults' => [
                        'controller' => 'Authorization\Controller\Manage',
                        'action' => 'tools'
                    ]
                ],
                'child_routes' => [
                    'role' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/role',
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Role',
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
                                    'route' => '/read[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
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
                                    'route' => '/update[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete[/:role_id][/confirm/:confirm]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+',
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
                                    'route' => '/activate[/:role_id][/confirm/:confirm]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+',
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
                                    'route' => '/deactivate[/:role_id][/confirm/:confirm]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'resource' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/resource',
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Resource',
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
                                    'route' => '/read[/:id]',
                                    'constraints' => [
                                        'id' => '[0-9]+'
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
                                    'route' => '/update[/:id]',
                                    'constraints' => [
                                        'id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete[/:id][/confirm/:confirm]',
                                    'constraints' => [
                                        'id' => '[0-9]+',
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
                                    'route' => '/activate[/:id][/confirm/:confirm]',
                                    'constraints' => [
                                        'id' => '[0-9]+',
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
                                    'route' => '/deactivate[/:id][/confirm/:confirm]',
                                    'constraints' => [
                                        'id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ],
                            'all-routes' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/all-routes',
                                    'defaults' => [
                                        'controller' => 'Authorization\Controller\Resource',
                                        'action' => 'all-routes'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'rule' => [
                        'type' => 'Literal',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/rule',
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Rule',
                                'action' => 'create'
                            ]
                        ],
                        'child_routes' => [
                            'add' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/add[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'add'
                                    ]
                                ]
                            ],
                            'read' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/read[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'read'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete/:rule_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'rule_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'delete'
                                    ]
                                ]
                            ],
                            'allow' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/allow/:rule_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'rule_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'allow'
                                    ]
                                ]
                            ],
                            'deny' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/deny/:rule_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'rule_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deny'
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
                                'controller' => 'Authorization\Controller\Learner',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'directory'
                                    ]
                                ]
                            ],
                            'add' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/add[/:role_id]',
                                    'constraints' => [
                                        'role_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'add'
                                    ]
                                ]
                            ],
                            'read' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/read/:user_id',
                                    'constraints' => [
                                        'user_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'read'
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