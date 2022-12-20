<?php

return [
    'router' => [
        'routes' => [
            'report' => [
                'child_routes' => [
                    'assessment-summary' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/assessment-summary',
                            'defaults' => [
                                'controller' => 'Report\AssessmentSummary\Controller\Manage',
                                'action' => 'list'
                            ]
                        ],
                        'child_routes' => [
                            'learners' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/learners/:event_id/:activity_id',
                                    'constraints' => [
                                        'event_id' => '[a-zA-Z0-9]+',
                                        'activity_id' => '[a-zA-Z0-9]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'learners'
                                    ]
                                ]
                            ],
                            'download' => [
                                'type' => 'Segment',
                                'may_terminate' => true,
                                'options' => [
                                    'route' => '/download/:pdf_id',
                                    'constraints' => [
                                        'pdf_id' => '[a-zA-Z][a-zA-Z0-9_-]+'
                                    ],
                                    'defaults' => [
                                        'action' => 'download'
                                    ]
                                ]
                            ],
                            'written' => [
                                'type' => 'Segment',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/written',
                                    'defaults' => [
                                        'action' => 'written'
                                    ]
                                ],
                                'child_routes' => [
                                    'export' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/export/:learner_id/:event_id/:activity_id',
                                            'constraints' => [
                                                'learner_id'    => '[a-zA-Z0-9]+',
                                                'event_id'      => '[a-zA-Z0-9]+',
                                                'activity_id'   => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'pdf'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'otj' => [
                                'type' => 'Segment',
                                'may_terminate' => false,
                                'options' => [
                                    'route' => '/otj',
                                    'defaults' => [
                                        'action' => 'otj'
                                    ]
                                ],
                                'child_routes' => [
                                    'export' => [
                                        'type' => 'Segment',
                                        'may_terminate' => false,
                                        'options' => [
                                            'route' => '/export/:learner_id/:event_id/:activity_id',
                                            'constraints' => [
                                                'learner_id'    => '[a-zA-Z0-9]+',
                                                'event_id'      => '[a-zA-Z0-9]+',
                                                'activity_id'   => '[a-zA-Z0-9]+'
                                            ],
                                            'defaults' => [
                                                'action' => 'pdfotj'
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
