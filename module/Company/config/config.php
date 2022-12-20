<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Company\Service' => 'Company\Factory\Service\CompanyServiceFactory',
            'Company\All'     => 'Company\Factory\Service\CompanyAllServiceFactory',
            'Company\Data'    => 'Company\Factory\Service\CompanyDataServiceFactory',
            'Company\One'     => 'Company\Factory\Service\CompanyEntityServiceFactory'
        ],
        'aliases' => [
            'Company' => 'Company\One',
            'Companies' => 'Company\All'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Company\Controller\Manage' => 'Company\Controller\ManageController'
        ]
    ],

    /**
     * FORM ELEMENT MANAGER
     */
    'form_elements' => [
        'factories' => [
            'Company\Form\New' => 'Company\Factory\Form\NewFormFactory',
            'Company\Form\Edit' => 'Company\Factory\Form\EditFormFactory'
        ],
        'initializers' => [
            'Company\Form\Initializer'
        ]
    ]
];