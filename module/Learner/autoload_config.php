<?php

return [
    'Zend\Loader\ClassMapAutoloader' => [
        __DIR__ . '/autoload_classmap.php'
    ],
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => [
            'Learner' => __DIR__ . '/Learner/src',
            'Authentication' => __DIR__ . '/Authentication/src',
            'Authorization' => __DIR__ . '/Authorization/src'
        ]
    ]
];