<?php

return [
    'router' => [
        'routes' => [
            'company' => [
                'type' => 'Literal',
                'may_terminate' => false,
                'options' => [
                    'route' => '/company',
                    'defaults' => [
                        'controller' => 'Company\Controller\Manage',
                        'action' => 'read'
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
                            'route' => '[/:company_id]',
                            'constraints' => [
                                'company_id' => '[0-9]{0,}'
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
                            'route' => '/update[/:company_id]',
                            'constraints' => [
                                'company_id' => '[0-9]{1,}'
                            ],
                            'defaults' => [
                                'action' => 'update'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];