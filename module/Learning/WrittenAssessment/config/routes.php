<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'child_routes' => [
                    'written-assessment' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/written-assessment',
                            'defaults' => [
                                'controller' => 'WrittenAssessment\Controller\Manage',
                                'action' => 'directory',
                                'activity_type' => 'written-assessment'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory',
                                    'defaults' => [
                                        'action' => 'directory',
                                        'activity_type' => 'written-assessment'
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
                                    'route' => '/:activity_id',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'read'
                                    ]
                                ]
                            ],

                            'event' => [
                                'type' => 'Literal',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/event',
                                    'defaults' => [
                                        'controller' => 'WrittenAssessment\Controller\Event',
                                        'action' => 'events'
                                    ]
                                ],
                                'child_routes' => [
                                    'directory' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/directory/:activity_id',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'directory'
                                            ]
                                        ]
                                    ],

                                    'create' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/create/:activity_id',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+'
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
                                            'route' => '/:event_id',
                                            'constraints' => [
                                                'event_id' => '[0-9]+'
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
                                            'route' => '/update/:event_id',
                                            'constraints' => [
                                                'event_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'update'
                                            ]
                                        ]
                                    ],

                                    'duplicate' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/duplicate/:event_id[/confirm/:confirm]',
                                            'constraints' => [
                                                'event_id' => '[0-9]+',
                                                'confirm' => '(yes|no)'
                                            ],
                                            'defaults' => [
                                                'action' => 'duplicate'
                                            ]
                                        ]
                                    ],

                                    'delete' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/delete/:event_id[/confirm/:confirm]',
                                            'constraints' => [
                                                'event_id' => '[0-9]+',
                                                'confirm' => '(yes|no)'
                                            ],
                                            'defaults' => [
                                                'action' => 'delete'
                                            ]
                                        ]
                                    ]
                                ]
                            ],

                            'update' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/update/:activity_id',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ]
                            ],

                            'duplicate' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/duplicate/:activity_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'duplicate'
                                    ]
                                ]
                            ],

                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete/:activity_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+',
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
                                    'route' => '/activate/:activity_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+',
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
                                    'route' => '/deactivate/:activity_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ],

                            'preview' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/preview/:activity_id',
                                    'constraints' => [
                                        'activity_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'controller' => 'WrittenAssessment\Controller\Questions',
                                        'action' => 'preview'
                                    ]
                                ]
                            ],

                            'questions' => [
                                'type' => 'Literal',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/questions',
                                    'defaults' => [
                                        'controller' => 'WrittenAssessment\Controller\Questions',
                                        'action' => 'create'
                                    ]
                                ],
                                'child_routes' => [
                                    'create' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/:activity_id',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'create'
                                            ]
                                        ]
                                    ],

                                    'preview' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/:activity_id/preview',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'preview'
                                            ]
                                        ]
                                    ],

                                    'read' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/question/:question_id',
                                            'constraints' => [
                                                'question_id' => '[0-9]+'
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
                                            'route' => '/delete/:question_id[/confirm/:confirm]',
                                            'constraints' => [
                                                'question_id' => '[0-9]+',
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