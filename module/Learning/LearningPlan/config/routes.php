<?php

return [
    'router' => [
        'routes' => [
            'learning' => [
                'child_routes' => [
                    'learning-plans' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/learning-plans',
                            'defaults' => [
                                'controller' => 'LearningPlan\Controller\Manage',
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
                                    'route' => '/:plan_id',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+'
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
                                    'route' => '/update/:plan_id',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'update'
                                    ]
                                ]
                            ],
                            'view' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/view/:plan_id',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'view'
                                    ]
                                ]
                            ],
                            'delete' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/delete/:plan_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+',
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
                                    'route' => '/activate/:plan_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+',
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
                                    'route' => '/deactivate/:plan_id[/confirm/:confirm]',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+',
                                        'confirm' => '(yes|no)'
                                    ],
                                    'defaults' => [
                                        'action' => 'deactivate'
                                    ]
                                ]
                            ],
                            'activities' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/activities/:plan_id',
                                    'constraints' => [
                                        'plan_id' => '[0-9]+'
                                    ],
                                    'defaults' => [
                                        'controller' => 'LearningPlan\Controller\Activities',
                                        'action' => 'activities'
                                    ]
                                ],
                                'child_routes' => [
                                    'save' => [
                                        'type' => 'Literal',
                                        'may_terminate' => true,
                                        'options' => [
                                            'route' => '/save',
                                            'defaults' => [
                                                'action' => 'save'
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