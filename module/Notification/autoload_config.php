<?php

return [
    'Zend\Loader\ClassMapAutoloader' => [
        __DIR__ . '/autoload_classmap.php'
    ],
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => [
            'Notification' => __DIR__ . '/Notification',
            'Notification\\Learner' => __DIR__ . '/Learner',
            'Notification\\Group' => __DIR__ . '/Group'
        ]
    ]
];