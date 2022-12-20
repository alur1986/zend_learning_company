<?php

return [
    'Zend\Loader\ClassMapAutoloader' => [
        __DIR__ . '/autoload_classmap.php'
    ],
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => [
            'Group' => __DIR__ . '/Group/src',
            'Group\\Learner' => __DIR__ . '/Learner/src',
        ]
    ]
];