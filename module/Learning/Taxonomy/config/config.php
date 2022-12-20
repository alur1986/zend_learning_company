<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Learning\Taxonomy\CategoryService' => 'Learning\Taxonomy\Factory\Service\CategoryServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Learning\Taxonomy\Controller\Category' => 'Learning\Taxonomy\Controller\CategoryController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
	'form_elements' => [
		'factories' => [
			'Learning\Taxonomy\Form\CreateCategory' => 'Learning\Taxonomy\Factory\Form\CreateCategoryFormFactory',
			'Learning\Taxonomy\Form\UpdateCategory' => 'Learning\Taxonomy\Factory\Form\UpdateCategoryFormFactory',
		]
	],

    /**
     * LISTENER MANAGER
     */
    'listener_manager' => [
        'invokables' => [
            'Learning\Taxonomy\EventManager\InjectTemplateListener' => 'Learning\Taxonomy\EventManager\InjectTemplateListener'
        ]
    ]
];