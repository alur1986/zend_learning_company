<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'child_routes' => [
                    'webinar' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/webinar',
                            'defaults' => [
                                'controller' => 'Webinar\Controller\Manage',
                                'action' => 'directory',
                                'activity_type' => 'webinar'
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
                                        'activity_type' => 'webinar'
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
                                        'controller' => 'Webinar\Controller\Event',
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
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];