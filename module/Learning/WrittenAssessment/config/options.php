<?php

return [
    'learning_options' => [
        'activity_types' => [
            'written-assessment' => [
                'title' => 'Written Assessment',
                'type' => 'written-assessment',
                'order' => 40,
                'events' => [
                    'allowed' => true,
                    'support' => [
                        'facilitator' => [
                            'allowed' => false
                        ],
                        'assessor' => [
                            'allowed' => true
                        ],
                        'vendor' => [
                            'allowed' => true
                        ],
                        'venue' => [
                            'allowed' => true
                        ]
                    ]
                ]
            ]
        ],

        'assessment_types' => ['written-assessment'],
    ]
];