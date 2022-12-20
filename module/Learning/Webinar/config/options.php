<?php

return [
    'learning_options' => [
        'activity_types' => [
            'webinar' => [
                'title' => 'Webinar',
                'type' => 'webinar',
                'order' => 20,
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

        'event_types' => ['webinar'],
    ]
];