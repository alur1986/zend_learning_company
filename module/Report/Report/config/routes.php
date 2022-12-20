<?php

return [
    'router' => [
        'routes' => [
            'report' => [
                'type' => 'Literal',
                'may_terminate' => true,
                'options' => [
                    'route' => '/report',
                    'defaults' => [
                        'controller' => 'Report\Controller\Manage',
                        'action' => 'tools'
                    ]
                ],
                'child_routes' => [
                    'tools' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/tools',
                            'defaults' => [
                                'action' => 'tools'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];