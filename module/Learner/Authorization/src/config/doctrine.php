<?php

return [
    /**
     * DOCTRINE ORM
     */
    'doctrine' => [
        // entity lifecycle event manager
        'eventmanager' => [
            'orm_default' => [
                // use either a FQCN or the service name created in the service manager
                'subscribers' => [
                    'Authorization\Doctrine\Event\Subscriber'
                ]
            ]
        ]
    ],

    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'invokables' => [
            'Authorization\Doctrine\Event\Subscriber' => 'Authorization\Doctrine\Event\Subscriber'
        ],
    ]
];