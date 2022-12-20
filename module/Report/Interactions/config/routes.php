<?php

return [
    'router' => [
        'routes' => [
            'report' => [
                'child_routes' => [
                    'interactions' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/interactions',
                            'defaults' => [
                                'controller' => 'Report\Interactions\Controller\Manage',
                                'action' => 'index'
                            ]
                        ]
                    ],
                    'xapi' => [
                        'type' => 'Literal',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/xapi',
                            'defaults' => [
                                'controller' => 'Report\Interactions\Controller\Manage',
                                'action' => 'xapiInteractions'
                            ]
                        ],
                        'child_routes' => [
                            'interactions' => [
                                'type' => 'Literal',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/interactions',
                                    'defaults' => [
                                        'action' => 'xapiInteractions'
                                    ]
                                ],
                                'child_routes' => [
                                    'download' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/download',
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
