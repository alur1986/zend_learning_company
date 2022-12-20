<?php

return [
    'learning_options' => [
        'activity_types' => [
            'face-to-face' => [
                'title' => 'Face-To-Face',
                'type' => 'face-to-face',
                'order' => 10,
                'events' => [
                    'allowed' => true,
                    'support' => [
                        'facilitator' => [
                            'allowed' => true
                        ],
                        'assessor' => [
                            'allowed' => false
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

        'event_types' => ['face-to-face'],
    ]
];