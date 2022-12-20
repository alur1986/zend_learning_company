<?php

return [
    'Zend\Loader\ClassMapAutoloader' => [
        __DIR__ . '/autoload_classmap.php'
    ],
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => [
            'Learning' => __DIR__ . '/Learning',
            'FaceToFace' => __DIR__ . '/FaceToFace',
            'OnTheJobAssessment' => __DIR__ . '/OnTheJobAssessment',
            'Resource' => __DIR__ . '/Resource',
            'Scorm12' => __DIR__ . '/Scorm12/src',
            'Tincan' => __DIR__ . '/Tincan/src',
            'Webinar' => __DIR__ . '/Webinar',
            'WrittenAssessment' => __DIR__ . '/WrittenAssessment'
        ]
    ]
];