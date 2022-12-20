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
                    'Learner\Doctrine\Event\Subscriber'
                ]
            ]
        ]
    ],

    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Learner\Doctrine\Event\Subscriber' => 'Learner\Factory\Doctrine\Event\SubscriberServiceFactory'
        ]
    ]
];