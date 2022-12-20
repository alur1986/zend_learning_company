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
                    'Notification\Doctrine\Event\Subscriber'
                ]
            ]
        ]
    ]
];