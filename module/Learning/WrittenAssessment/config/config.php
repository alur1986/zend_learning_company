<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'WrittenAssessment\Service' => 'WrittenAssessment\Factory\Service\WrittenAssessmentServiceFactory',
            'WrittenAssessment\QuestionService' => 'WrittenAssessment\Factory\Service\QuestionServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'WrittenAssessment\Controller\Manage' => 'WrittenAssessment\Controller\ManageController',
            'WrittenAssessment\Controller\Questions' => 'WrittenAssessment\Controller\QuestionsController',
            'WrittenAssessment\Controller\Event' => 'WrittenAssessment\Controller\EventController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'WrittenAssessment\Form\Create' => 'WrittenAssessment\Factory\Form\CreateActivityFormFactory',
            'WrittenAssessment\Form\Update' => 'WrittenAssessment\Factory\Form\UpdateActivityFormFactory',
            'WrittenAssessment\Form\CreateQuestion' => 'WrittenAssessment\Factory\Form\CreateQuestionFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'identifier' => 'Learning\Service\LearningService',
            'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
            'callback' => 'WrittenAssessment\EventManager\Listener\DuplicateActivityListener'
        ]
    ]
];