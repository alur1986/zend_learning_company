<?php

return [
    'router' => [
        'routes' => [
            'group' => [
                'child_routes' => [
                    'learner' => [
                        'type' => 'Literal',
                        'may_terminate' => false,
                        'options' => [
                            'route' => '/learner',
                            'defaults' => [
                                'controller' => 'Group\Learner\Controller\Manage',
                                'action' => 'directory'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory/:group_id',
                                    'constraints' => [
                                        'group_id' => '[0-9]{1,}'
                                    ],
                                    'defaults' => [
                                        'action' => 'directory'
                                    ]
                                ]
                            ],

                            'learners' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/learners/:group_id',
                                    'constraints' => [
                                        'group_id' => '[0-9]{1,}'
                                    ],
                                    'defaults' => [
                                        'action' => 'learners'
                                    ]
                                ]
                            ],

                            'import' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/import/:group_id',
                                    'constraints' => [
                                        'group_id' => '[0-9]{1,}'
                                    ],
                                    'defaults' => [
                                        'action' => 'import'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            /**
             * LEARNER
             */
            'learner' => [
                'child_routes' => [
                    'groups' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/groups[/:user_id]',
                            'constraints' => [
                                'user_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'controller' => 'Group\Learner\Controller\Learner',
                                'action' => 'groups'
                            ]
                        ],
                        'child_routes' => [
                            'directory' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/directory',
                                    'defaults' => [
                                        'action' => 'groups'
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