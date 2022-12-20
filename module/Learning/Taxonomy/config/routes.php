<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'child_routes' => [
                    'category' => [
                        'type' => 'Literal',
                        'my_terminate' => true,
                        'options' => [
                            'route' => '/category',
                            'defaults' => [
                                'controller' => 'Learning\Taxonomy\Controller\Category',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Literal',
                                'my_terminate' => true,
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
                                    'route' => '/:category_id',
                                    'constraints' => [
                                        'category_id' => '[0-9]+'
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
                                    'route' => '/update/:category_id',
                                    'constraints' => [
                                        'category_id' => '[0-9]+'
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
                                    'route' => '/delete/:category_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'category_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'delete'
                                    ]
                                ]
                            ],

                            'permalink' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'priority' => -1,
                                'options' => [
                                    'route' => '/:slug',
                                    'constraints' => [
                                        'slug' => '[a-zA-Z0-9\-]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'permalink'
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