<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'OnTheJobAssessment\Service' => 'OnTheJobAssessment\Factory\Service\OnTheJobAssessmentServiceFactory',
            'OnTheJobAssessment\QuestionService' => 'OnTheJobAssessment\Factory\Service\QuestionServiceFactory'
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'OnTheJobAssessment\Controller\Manage' => 'OnTheJobAssessment\Controller\ManageController',
            'OnTheJobAssessment\Controller\Questions' => 'OnTheJobAssessment\Controller\QuestionsController',
            'OnTheJobAssessment\Controller\Event' => 'OnTheJobAssessment\Controller\EventController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'OnTheJobAssessment\Form\Create' => 'OnTheJobAssessment\Factory\Form\CreateOnTheJobAssessmentFormFactory',
            'OnTheJobAssessment\Form\Update' => 'OnTheJobAssessment\Factory\Form\UpdateOnTheJobAssessmentFormFactory',
            'OnTheJobAssessment\Form\CreateQuestion' => 'OnTheJobAssessment\Factory\Form\CreateQuestionFormFactory'
        ]
    ],

    /**
     * EVENT LISTENERS
     */
    'event_listeners' => [
        [
            'identifier' => 'Learning\Service\LearningService',
            'event' => \Learning\EventManager\Event::EVENT_DUPLICATE,
            'callback' => 'OnTheJobAssessment\EventManager\Listener\DuplicateActivityListener'
        ]
    ]
];