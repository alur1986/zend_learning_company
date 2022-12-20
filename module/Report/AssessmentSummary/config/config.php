<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Report\AssessmentSummary\Service' => 'Report\AssessmentSummary\Factory\Service\ReportServiceFactory'
        ],
        'delegators' => []
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Report\AssessmentSummary\Controller\Manage' => 'Report\AssessmentSummary\Controller\ManageController'
        ]
    ],

    /**
     * LISTENER MANAGERS
     */
    'listener_manager' => [
        'invokables' => [
            'Report\AssessmentSummary\EventManager\InjectTemplateListener' => 'Report\AssessmentSummary\EventManager\InjectTemplateListener'
        ]
    ]
];