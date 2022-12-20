<?php

return [
    'router' => [
        'routes' => [
            'report' => [
                'child_routes' => [
                    'mylocker' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/mylocker',
                            'defaults' => [
                                'controller' => 'Report\MyLocker\Controller\Manage',
                                'action' => 'start'
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

                            'start' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/start',
                                    'defaults' => [
                                        'action' => 'start'
                                    ]
                                ]
                            ],

                            'categories' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/categories[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'categories'
                                    ]
                                ]
                            ],

                            'range' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/range[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'range'
                                    ]
                                ]
                            ],

                            'report' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/report[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'report'
                                    ]
                                ]
                            ],

                            'export' => [
                                'type' => 'Segment',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/export[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
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
                                    ],

                                    'pdf' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/pdf',
                                            'defaults' => [
                                                'action' => 'pdf'
                                            ]
                                        ]
                                    ]
                                ]
                            ],

                            'print' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/print[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'print'
                                    ]
                                ]
                            ],

                            'filter' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/filter',
                                    'defaults' => [
                                        'controller' => 'Report\MyLocker\Controller\Filter',
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
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/create[/:session_id]',
                                            'constraints' => [
                                                'session_id' => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'create'
                                            ]
                                        ]
                                    ],

                                    'read' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
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
                                            'route' => '/update/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'update'
                                            ]
                                        ]
                                    ],

                                    'categories' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/categories/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'categories'
                                            ]
                                        ]
                                    ],

                                    'range' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/range/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'range'
                                            ]
                                        ]
                                    ],

                                    'execute' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/execute/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'execute'
                                            ]
                                        ]
                                    ],

                                    'delete' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/delete/:filter_id[/confirm/:confirm]',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+',
                                                'confirm' => '(yes|no)'
                                            ],
                                            'defaults' => [
                                                'action' => 'delete'
                                            ]
                                        ]
                                    ]
                                ]
                            ],

                            'template' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/template',
                                    'defaults' => [
                                        'controller' => 'Report\MyLocker\Controller\Template',
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
                                            'route' => '/:template_id',
                                            'constraints' => [
                                                'template_id' => '[0-9]+'
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
                                            'route' => '/update/:template_id',
                                            'constraints' => [
                                                'template_id' => '[0-9]+'
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
                                            'route' => '/delete/:template_id[/confirm/:confirm]',
                                            'constraints' => [
                                                'template_id' => '[0-9]+',
                                                'confirm' => '(yes|no)'
                                            ],
                                            'defaults' => [
                                                'action' => 'delete'
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