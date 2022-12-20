<?php

return [
    'router' => [
        'routes' => [
            'report' => [
                'child_routes' => [
                    'myexperience' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/myexperience',
                            'defaults' => [
                                'controller' => 'Report\MyExperience\Controller\Manage',
                                'action' => 'start'
                            ]
                        ],

                        'child_routes' => [

                            'activities' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/activities[/:session_id]',
                                    'constraints' => [
                                        'session_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'activities'
                                    ]
                                ]
                            ],

                            'aggregate' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/aggregate[/:distribution_id]',
                                    'constraints' => [
                                        'distribution_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'aggregate'
                                    ]
                                ]
                            ],

                            'individual' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/individual[/:distribution_id]',
                                    'constraints' => [
                                        'distribution_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'individual'
                                    ]
                                ]
                            ],

                            'experience' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/experience[/:distribution_id]',
                                    'constraints' => [
                                        'distribution_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'experience'
                                    ]
                                ]
                            ],

                            'interaction' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/interaction[/:distribution_id]',
                                    'constraints' => [
                                        'distribution_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'individual'
                                    ]
                                ]
                            ],

                            'print' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/print/:learner_id/:activity_id',
                                    'constraints' => [
                                        'learner_id' => '[a-zA-Z0-9]+',
                                        'activity_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'print'
                                    ]
                                ]
                            ],

                            'overview' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/overview/:learner_id',
                                    'constraints' => [
                                        'learner_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'overview'
                                    ]
                                ],
                                'child_routes' => [

                                    'aggregate' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/aggregate[/:distribution_id]',
                                            'constraints' => [
                                                'learner_id' => '[a-zA-Z0-9]+',
                                                'distribution_id' => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'aggregateAdmin'
                                            ]
                                        ]
                                    ],

                                    'individual' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/individual[/:distribution_id]',
                                            'constraints' => [
                                                'distribution_id' => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'individual'
                                            ]
                                        ]
                                    ]
                                ]
                            ],

                            'reports' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/reports',
                                    'defaults' => [
                                        'controller' => 'Report\MyExperience\Controller\Report',
                                        'action' => 'tools'
                                    ]
                                ],
                                'child_routes' => [

                                    /*'inspire' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/inspire',
                                            'defaults' => [
                                                'controller' => 'Report\MyExperience\Controller\InspireReport',
                                                'action' => 'directory'
                                            ]
                                        ],
                                        'child_routes' => [
                                            'view' => [
                                                'type' => 'Segment',
                                                'may_terminate' => true,
                                                'options' => [
                                                    'route' => '/view[/:session_id]',
                                                    'constraints' => [
                                                        'session_id' => '[a-zA-Z0-9]+'
                                                    ],
                                                    'defaults' => [
                                                        'action' => 'view'
                                                    ]
                                                ]
                                            ],
                                            'export' => [
                                                'type' => 'Segment',
                                                'may_terminate' => false,
                                                'options' => [
                                                    'route' => '/export/:session_id',
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
                                            ]
                                        ]
                                    ],*/

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

                                    'directory' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/directory[/:session_id]',
                                            'constraints' => [
                                                'session_id' => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'directory'
                                            ]
                                        ]
                                    ],

                                    'view' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/view[/:session_id]',
                                            'constraints' => [
                                                'session_id' => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'view'
                                            ]
                                        ]
                                    ],

                                    'export' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/export/:session_id',
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
                                    ]
                                ]
                            ],

                            'filter' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/filter',
                                    'defaults' => [
                                        'controller' => 'Report\MyExperience\Controller\Filter',
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

                                    'activities' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/activities/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'activities'
                                            ]
                                        ]
                                    ],

                                    'events' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/events/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'events'
                                            ]
                                        ]
                                    ],

                                    'groups' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/groups/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'groups'
                                            ]
                                        ]
                                    ],

                                    'learners' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/learners/:filter_id',
                                            'constraints' => [
                                                'filter_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'learners'
                                            ]
                                        ]
                                    ],

                                    'status' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/status/:filter_id',
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
                                        'controller' => 'Report\MyExperience\Controller\Template',
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
