<?php

return [
    /**
     * SERVICE MANAGER
     */
    'service_manager' => [
        'factories' => [
            'Group\Learner\Service' => 'Group\Learner\Factory\Service\GroupLearnerServiceFactory',
            'Group\Learner\Options' => 'Group\Learner\Factory\Service\OptionFactory',
            'Group\Learner\All' => 'Group\Learner\Factory\Service\MembersServiceFactory',
            'Group\Learner\Admins' => 'Group\Learner\Factory\Service\GroupAdminsServiceFactory',
            'Group\Learner\Learner\Groups' => 'Group\Learner\Factory\Service\GroupLearnerGroupsServiceFactory',
            'Group\Learner\Admin\Groups' => 'Group\Learner\Factory\Service\GroupAdminGroupsServiceFactory'
        ],
        'aliases' => [
            'Group\Admin\Groups' => 'Group\Learner\Admin\Groups',
            'Group\Learner\Groups' => 'Group\Learner\Learner\Groups'
        ],
        'delegators' => [
            'Learner\All' => [
                'Group\Learner\Factory\Service\Delegator\FilterMembersDelegatorFactory',
                'Group\Learner\Factory\Service\Delegator\FilterNonMembersDelegatorFactory'
            ]
        ]
    ],

    /**
     * CONTROLLERS
     */
    'controllers' => [
        'invokables' => [
            'Group\Learner\Controller\Manage' => 'Group\Learner\Controller\ManageController',
            'Group\Learner\Controller\Learner' => 'Group\Learner\Controller\LearnerController'
        ]
    ],

    /**
     * FORM ELEMENTS
     */
    'form_elements' => [
        'factories' => [
            'Group\Learner\Form\LearnersUpdate' => 'Group\Learner\Factory\Form\UpdateLearnersFormFactory',
            'Group\Learner\Form\LearnersAdd' => 'Group\Learner\Factory\Form\AddLearnersFormFactory',
            'Group\Learner\Form\Import' => 'Group\Learner\Factory\Form\ImportFormFactory',
            'Group\Learner\Form\AddLearnerToGroup' => 'Group\Learner\Factory\Form\AddLearnerToGroupFormFactory'
        ]
    ],

    /**
     * LISTENER MANAGER
     */
    'listener_manager' => [
        'invokables' => [
            'Group\Learner\EventManager\InjectTemplateListener' => 'Group\Learner\EventManager\InjectTemplateListener'
        ]
    ]
];