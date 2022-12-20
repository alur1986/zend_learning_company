<?php

return [
    /**
     * DOCTRINE ORM
     */
    'doctrine' => [
        'eventmanager' => [
            'orm_default' => [
                // use either a FQCN or the service name created in the service manager
                'subscribers' => [
                    'Resource\Doctrine\Event\Subscriber\Subscriber'
                ]
            ]
        ],
    ],

    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
    	'factories' => [
    		'Resource\Doctrine\Event\Subscriber\Subscriber' => 'Resource\Factory\Doctrine\Event\Subscriber\SubscriberServiceFactory'
    	]
    ]
];