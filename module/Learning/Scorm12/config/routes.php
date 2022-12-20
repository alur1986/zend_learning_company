<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'child_routes' => [
                    'scorm12' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/scorm12',
                            'defaults' => [
                                'controller' => 'Scorm12\Controller\Manage',
                                'action' => 'directory',
                                'activity_type' => 'scorm12'
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
                                        'activity_type' => 'scorm12'
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

                            'file' => [
                                'type' => 'Literal',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/file',
                                    'defaults' => [
                                        'controller' => 'Scorm12\Controller\File',
                                        'action' => 'upload'
                                    ]
                                ],
                                'child_routes' => [
                                    'upload' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/:activity_id',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'upload'
                                            ]
                                        ]
                                    ],

                                    'download' => [
                                        'type' => 'Segment',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/download/:activity_id[/:filename]',
                                            'constraints' => [
                                                'activity_id' => '[0-9]+',
                                                'filename' => '[a-zA-Z0-9\-_\.]+\.[a-zA-Z0-9]{3,}'
                                            ],
                                            'defaults' => [
                                                'action' => 'download'
                                            ]
                                        ]
                                    ],
                                ]
                            ],

                            'course' => [
                                'type' => 'Literal',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/course',
                                    'defaults' => [
                                        'controller' => 'Scorm12\Controller\Item',
                                        'action' => 'directory'
                                    ]
                                ],
                                'child_routes' => [
                                    'item' => [
                                        'type' => 'Literal',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/item',
                                            'defaults' => [
                                                'action' => 'directory'
                                            ]
                                        ],
                                        'child_routes' => [
                                            'directory' => [
                                                'type' => 'Segment',
                                                'may_terminate' => true,
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

                                            'read' => [
                                                'type' => 'Segment',
                                                'may_terminate' => true,
                                                'options' => [
                                                    'route' => '/:item_id',
                                                    'constraints' => [
                                                        'item_id' => '[0-9]+'
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
                                                    'route' => '/update/:item_id',
                                                    'constraints' => [
                                                        'item_id' => '[0-9]+'
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
                                                    'route' => '/delete/:item_id[/confirm/:confirm]',
                                                    'constraints' => [
                                                        'item_id' => '[0-9]+',
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
        ]
    ]
];