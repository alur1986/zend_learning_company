<?php

return [
    'router' => [
        'routes' => [
            'agent' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/agent',
                    'defaults' => [
                        'controller' => 'Agent\Controller\Manage',
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
                            'route' => '[/:agent_id]',
                            'constraints' => [
                                'agent_id' => '[0-9]{0,}'
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
                            'route' => '/update[/:agent_id]',
                            'constraints' => [
                                'agent_id' => '[0-9]{1,}'
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
                            'route' => '/delete/:agent_id[/confirm/:confirm]',
                            'constraints' => [
                                'agent_id' => '[0-9]{1,}',
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
];
