<?php

return [
    'learning_options' => [
        'activity_types' => [
            'on-the-job-assessment' => [
                'title' => 'On-The-Job Assessment',
                'type' => 'on-the-job-assessment',
                'order' => 50,
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

        'assessment_types' => ['on-the-job-assessment'],
    ]
];