<?php

return [
    'learning_options' => [
        'activity_types' => [
            'resource' => [
                'title' => 'Resource',
                'type' => 'resource',
                'order' => 30,
                'events' => [
                    'allowed' => false,
                    'support' => [
                        'facilitator' => [
                            'allowed' => false
                        ],
                        'assessor' => [
                            'allowed' => false
                        ],
                        'vendor' => [
                            'allowed' => false
                        ],
                        'venue' => [
                            'allowed' => false
                        ]
                    ]
                ]
            ]
        ],

        'learning_types' => [
            'resource'
        ]
    ]
];