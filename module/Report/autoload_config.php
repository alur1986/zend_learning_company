<?php

return [
    'Zend\Loader\ClassMapAutoloader' => [
        __DIR__ . '/autoload_classmap.php'
    ],
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => [
            'Report' => __DIR__ . '/Report',
            'Report\\AssessmentSummary' => __DIR__ . '/AssessmentSummary',
            'Report\\EventProgressDetails' => __DIR__ . '/EventProgressDetails',
            'Report\\EventProgressSummary' => __DIR__ . '/EventProgressSummary',
            'Report\\LearningProgressDetails' => __DIR__ . '/LearningProgressDetails',
            'Report\\LearningProgressSummary' => __DIR__ . '/LearningProgressSummary',
            'Report\\MyLearning' => __DIR__ . '/MyLearning',
            'Report\\IndividualLearning' => __DIR__ . '/IndividualLearning',
            'Report\\MyLocker' => __DIR__ . '/MyLocker',
            'Report\\IndividualLocker' => __DIR__ . '/IndividualLocker',
            'Report\\Interactions' => __DIR__ . '/Interactions'
        ]
    ]
];
