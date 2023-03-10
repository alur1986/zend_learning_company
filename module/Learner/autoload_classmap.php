<?php
// Generated by ZF2's ./bin/classmap_generator.php
return array(
    'Authentication\Controller\AuthenticationController'                                            => __DIR__ . '/Authentication/src/Controller/AuthenticationController.php',
    'Authentication\Doctrine\Adapter\ObjectRepository'                                              => __DIR__ . '/Authentication/src/Doctrine/Adapter/ObjectRepository.php',
    'Authentication\Doctrine\Credential'                                                            => __DIR__ . '/Authentication/src/Doctrine/Credential.php',
    'Authentication\Doctrine\Storage\ObjectRepository'                                              => __DIR__ . '/Authentication/src/Doctrine/Storage/ObjectRepository.php',
    'Authentication\EventManager\AuthenticationInjectTemplateListener'                              => __DIR__ . '/Authentication/src/EventManager/AuthenticationInjectTemplateListener.php',
    'Authentication\EventManager\ListenerAggregate'                                                 => __DIR__ . '/Authentication/src/EventManager/ListenerAggregate.php',
    'Authentication\EventManager\Listener\RouteListener'                                            => __DIR__ . '/Authentication/src/EventManager/Listener/RouteListener.php',
    'Authentication\Factory\Authentication\AdapterFactory'                                          => __DIR__ . '/Authentication/src/Factory/Authentication/AdapterFactory.php',
    'Authentication\Factory\Authentication\StorageFactory'                                          => __DIR__ . '/Authentication/src/Factory/Authentication/StorageFactory.php',
    'Authentication\Factory\Form\LoginFormFactory'                                                  => __DIR__ . '/Authentication/src/Factory/Form/LoginFormFactory.php',
    'Authentication\Factory\Service\AuthenticationServiceFactory'                                   => __DIR__ . '/Authentication/src/Factory/Service/AuthenticationServiceFactory.php',
    'Authentication\Factory\Service\LoggedInLearnerServiceFactory'                                  => __DIR__ . '/Authentication/src/Factory/Service/LoggedInLearnerServiceFactory.php',
    'Authentication\Factory\Service\OptionsServiceFactory'                                          => __DIR__ . '/Authentication/src/Factory/Service/OptionsServiceFactory.php',
    'Authentication\Form\LoginForm'                                                                 => __DIR__ . '/Authentication/src/Form/LoginForm.php',
    'Authentication\Hydrator\Authentication'                                                        => __DIR__ . '/Authentication/src/Hydrator/Authentication.php',
    'Authentication\InputFilter\Authentication'                                                     => __DIR__ . '/Authentication/src/InputFilter/Authentication.php',
    'Authentication\Service\AuthenticationService'                                                  => __DIR__ . '/Authentication/src/Service/AuthenticationService.php',
    'Authentication\Service\Options'                                                                => __DIR__ . '/Authentication/src/Service/Options.php',
    'Authentication\Storage\Session'                                                                => __DIR__ . '/Authentication/src/Storage/Session.php',
    'Authentication\View\Helper\LoggedIn'                                                           => __DIR__ . '/Authentication/src/View/Helper/LoggedIn.php',
    'Authorization\Assertion\AbstractAssertion'                                                     => __DIR__ . '/Authorization/src/Assertion/AbstractAssertion.php',
    'Authorization\Assertion\AssertionManager\AssertionProviderInterface'                           => __DIR__ . '/Authorization/src/Assertion/AssertionManager/AssertionProviderInterface.php',
    'Authorization\Controller\LearnerController'                                                    => __DIR__ . '/Authorization/src/Controller/LearnerController.php',
    'Authorization\Controller\ManageController'                                                     => __DIR__ . '/Authorization/src/Controller/ManageController.php',
    'Authorization\Controller\ResourceController'                                                   => __DIR__ . '/Authorization/src/Controller/ResourceController.php',
    'Authorization\Controller\RoleController'                                                       => __DIR__ . '/Authorization/src/Controller/RoleController.php',
    'Authorization\Controller\RuleController'                                                       => __DIR__ . '/Authorization/src/Controller/RuleController.php',
    'Authorization\Doctrine\Event\Subscriber'                                                       => __DIR__ . '/Authorization/src/Doctrine/Event/Subscriber.php',
    'Authorization\EventManager\InjectTemplateListener'                                             => __DIR__ . '/Authorization/src/EventManager/InjectTemplateListener.php',
    'Authorization\EventManager\Listener\RouteListener'                                             => __DIR__ . '/Authorization/src/EventManager/Listener/RouteListener.php',
    'Authorization\Factory\Form\Learner\LearnerFormFactory'                                         => __DIR__ . '/Authorization/src/Factory/Form/Learner/LearnerFormFactory.php',
    'Authorization\Factory\Form\Resource\CreateFormFactory'                                         => __DIR__ . '/Authorization/src/Factory/Form/Resource/CreateFormFactory.php',
    'Authorization\Factory\Form\Resource\UpdateFormFactory'                                         => __DIR__ . '/Authorization/src/Factory/Form/Resource/UpdateFormFactory.php',
    'Authorization\Factory\Form\Role\CreateFormFactory'                                             => __DIR__ . '/Authorization/src/Factory/Form/Role/CreateFormFactory.php',
    'Authorization\Factory\Form\Role\UpdateFormFactory'                                             => __DIR__ . '/Authorization/src/Factory/Form/Role/UpdateFormFactory.php',
    'Authorization\Factory\Form\Rule\CreateFormFactory'                                             => __DIR__ . '/Authorization/src/Factory/Form/Rule/CreateFormFactory.php',
    'Authorization\Factory\Form\Rule\UpdateFormFactory'                                             => __DIR__ . '/Authorization/src/Factory/Form/Rule/UpdateFormFactory.php',
    'Authorization\Factory\Guard\GuardsServiceProviderFactory'                                      => __DIR__ . '/Authorization/src/Factory/Guard/GuardsServiceProviderFactory.php',
    'Authorization\Factory\Guard\RouteGuardFactory'                                                 => __DIR__ . '/Authorization/src/Factory/Guard/RouteGuardFactory.php',
    'Authorization\Factory\Guard\UriGuardFactory'                                                   => __DIR__ . '/Authorization/src/Factory/Guard/UriGuardFactory.php',
    'Authorization\Factory\Guard\ViewModelGuardFactory'                                             => __DIR__ . '/Authorization/src/Factory/Guard/ViewModelGuardFactory.php',
    'Authorization\Factory\Mvc\Controller\Plugin\IsGrantedPluginFactory'                            => __DIR__ . '/Authorization/src/Factory/Mvc/Controller/Plugin/IsGrantedPluginFactory.php',
    'Authorization\Factory\Service\AuthorizationServiceFactory'                                     => __DIR__ . '/Authorization/src/Factory/Service/AuthorizationServiceFactory.php',
    'Authorization\Factory\Service\Level\AllLevelsServiceFactory'                                   => __DIR__ . '/Authorization/src/Factory/Service/Level/AllLevelsServiceFactory.php',
    'Authorization\Factory\Service\OptionsServiceFactory'                                           => __DIR__ . '/Authorization/src/Factory/Service/OptionsServiceFactory.php',
    'Authorization\Factory\Service\Resource\AllResourcesServiceFactory'                             => __DIR__ . '/Authorization/src/Factory/Service/Resource/AllResourcesServiceFactory.php',
    'Authorization\Factory\Service\Resource\Delegator\FilterByLevelResourceServiceDelegatorFactory' => __DIR__ . '/Authorization/src/Factory/Service/Resource/Delegator/FilterByLevelResourceServiceDelegatorFactory.php',
    'Authorization\Factory\Service\Role\AllRolesServiceFactory'                                     => __DIR__ . '/Authorization/src/Factory/Service/Role/AllRolesServiceFactory.php',
    'Authorization\Factory\Service\Role\Delegator\FilterByLevelRoleServiceDelegatorFactory'         => __DIR__ . '/Authorization/src/Factory/Service/Role/Delegator/FilterByLevelRoleServiceDelegatorFactory.php',
    'Authorization\Factory\Service\Role\LearnersServiceFactory'                                     => __DIR__ . '/Authorization/src/Factory/Service/Role/LearnersServiceFactory.php',
    'Authorization\Factory\Service\Role\OneRoleServiceFactory'                                      => __DIR__ . '/Authorization/src/Factory/Service/Role/OneRoleServiceFactory.php',
    'Authorization\Factory\Service\Role\RulesServiceFactory'                                        => __DIR__ . '/Authorization/src/Factory/Service/Role/RulesServiceFactory.php',
    'Authorization\Factory\View\Helper\IsGrantedHelperFactory'                                      => __DIR__ . '/Authorization/src/Factory/View/Helper/IsGrantedHelperFactory.php',
    'Authorization\Factory\View\Helper\RoleHelperFactory'                                           => __DIR__ . '/Authorization/src/Factory/View/Helper/RoleHelperFactory.php',
    'Authorization\Form\Learner\Form'                                                               => __DIR__ . '/Authorization/src/Form/Learner/Form.php',
    'Authorization\Form\Level\Initialiser'                                                          => __DIR__ . '/Authorization/src/Form/Level/Initialiser.php',
    'Authorization\Form\Resource\Form'                                                              => __DIR__ . '/Authorization/src/Form/Resource/Form.php',
    'Authorization\Form\Resource\Initialiser'                                                       => __DIR__ . '/Authorization/src/Form/Resource/Initialiser.php',
    'Authorization\Form\Role\Form'                                                                  => __DIR__ . '/Authorization/src/Form/Role/Form.php',
    'Authorization\Form\Role\Initialiser'                                                           => __DIR__ . '/Authorization/src/Form/Role/Initialiser.php',
    'Authorization\Form\Rule\Form'                                                                  => __DIR__ . '/Authorization/src/Form/Rule/Form.php',
    'Authorization\Guard\AbstractGuard'                                                             => __DIR__ . '/Authorization/src/Guard/AbstractGuard.php',
    'Authorization\Guard\ControllerGuard'                                                           => __DIR__ . '/Authorization/src/Guard/ControllerGuard.php',
    'Authorization\Guard\GuardManager\GuardProviderInterface'                                       => __DIR__ . '/Authorization/src/Guard/GuardManager/GuardProviderInterface.php',
    'Authorization\Guard\GuardManager\GuardProviderPluginManager'                                   => __DIR__ . '/Authorization/src/Guard/GuardManager/GuardProviderPluginManager.php',
    'Authorization\Guard\GuardManager\GuardProviderPluginManagerFactory'                            => __DIR__ . '/Authorization/src/Guard/GuardManager/GuardProviderPluginManagerFactory.php',
    'Authorization\Guard\RouteGuard'                                                                => __DIR__ . '/Authorization/src/Guard/RouteGuard.php',
    'Authorization\Guard\UriGuard'                                                                  => __DIR__ . '/Authorization/src/Guard/UriGuard.php',
    'Authorization\Guard\ViewModelGuard'                                                            => __DIR__ . '/Authorization/src/Guard/ViewModelGuard.php',
    'Authorization\Hydrator\Learner\AggregateHydrator'                                              => __DIR__ . '/Authorization/src/Hydrator/Learner/AggregateHydrator.php',
    'Authorization\Hydrator\Resource\AggregateHydrator'                                             => __DIR__ . '/Authorization/src/Hydrator/Resource/AggregateHydrator.php',
    'Authorization\Hydrator\Role\AggregateHydrator'                                                 => __DIR__ . '/Authorization/src/Hydrator/Role/AggregateHydrator.php',
    'Authorization\Hydrator\Rule\AggregateHydrator'                                                 => __DIR__ . '/Authorization/src/Hydrator/Rule/AggregateHydrator.php',
    'Authorization\InputFilter\Learner\InputFilter'                                                 => __DIR__ . '/Authorization/src/InputFilter/Learner/InputFilter.php',
    'Authorization\InputFilter\Resource\InputFilter'                                                => __DIR__ . '/Authorization/src/InputFilter/Resource/InputFilter.php',
    'Authorization\InputFilter\Role\InputFilter'                                                    => __DIR__ . '/Authorization/src/InputFilter/Role/InputFilter.php',
    'Authorization\InputFilter\Rule\InputFilter'                                                    => __DIR__ . '/Authorization/src/InputFilter/Rule/InputFilter.php',
    'Authorization\Mvc\Controller\Plugin\IsGranted'                                                 => __DIR__ . '/Authorization/src/Mvc/Controller/Plugin/IsGranted.php',
    'Authorization\Role\AbstractRole'                                                               => __DIR__ . '/Authorization/src/Role/AbstractRole.php',
    'Authorization\Role\SuperAdmin'                                                                 => __DIR__ . '/Authorization/src/Role/SuperAdmin.php',
    'Authorization\Rule\AbstractRule'                                                               => __DIR__ . '/Authorization/src/Rule/AbstractRule.php',
    'Authorization\Rule\Allow'                                                                      => __DIR__ . '/Authorization/src/Rule/Allow.php',
    'Authorization\Rule\Deny'                                                                       => __DIR__ . '/Authorization/src/Rule/Deny.php',
    'Authorization\Service\AuthorizationService'                                                    => __DIR__ . '/Authorization/src/Service/AuthorizationService.php',
    'Authorization\Service\Options'                                                                 => __DIR__ . '/Authorization/src/Service/Options.php',
    'Authorization\Stdlib\Authorization'                                                            => __DIR__ . '/Authorization/src/Stdlib/Authorization.php',
    'Authorization\Stdlib\Role'                                                                     => __DIR__ . '/Authorization/src/Stdlib/Role.php',
    'Authorization\View\Helper\IsGranted'                                                           => __DIR__ . '/Authorization/src/View/Helper/IsGranted.php',
    'Authorization\View\Helper\Role'                                                                => __DIR__ . '/Authorization/src/View/Helper/Role.php',
    'Learner\Controller\AdminController'                                                            => __DIR__ . '/Learner/src/Controller/AdminController.php',
    'Learner\Controller\EmploymentController'                                                       => __DIR__ . '/Learner/src/Controller/EmploymentController.php',
    'Learner\Controller\LearnerController'                                                          => __DIR__ . '/Learner/src/Controller/LearnerController.php',
    'Learner\Controller\PhotoController'                                                            => __DIR__ . '/Learner/src/Controller/PhotoController.php',
    'Learner\Controller\SettingsController'                                                         => __DIR__ . '/Learner/src/Controller/SettingsController.php',
    'Learner\Controller\DistributionControler'                                                      => __DIR__ . '/Learner/src/Controller/DistributionController.php',
    'Learner\Doctrine\Event\Subscriber'                                                             => __DIR__ . '/Learner/src/Doctrine/Event/Subscriber.php',
    'Learner\EventManager\Listener\EventListener'                                                   => __DIR__ . '/Learner/src/EventManager/Listener/EventListener.php',
    'Learner\Event\Event'                                                                           => __DIR__ . '/Learner/src/Event/Event.php',
    'Learner\Event\LearnerInjectTemplateListener'                                                   => __DIR__ . '/Learner/src/Event/LearnerInjectTemplateListener.php',
    'Learner\Event\ListenerAggregate'                                                               => __DIR__ . '/Learner/src/Event/ListenerAggregate.php',
    'Learner\Event\Listener\Test'                                                                   => __DIR__ . '/Learner/src/Event/Listener/Test.php',
    'Learner\Exception\InvalidArgumentException'                                                    => __DIR__ . '/Learner/src/Exception/InvalidArgumentException.php',
    'Learner\Exception\LearnerNotFoundException'                                                    => __DIR__ . '/Learner/src/Exception/LearnerNotFoundException.php',
    'Learner\Factory\Doctrine\Event\SubscriberServiceFactory'                                       => __DIR__ . '/Learner/src/Factory/Doctrine/Event/SubscriberServiceFactory.php',
    'Learner\Factory\Form\CreateFormFactory'                                                        => __DIR__ . '/Learner/src/Factory/Form/CreateFormFactory.php',
    'Learner\Factory\Form\EditFormFactory'                                                          => __DIR__ . '/Learner/src/Factory/Form/EditFormFactory.php',
    'Learner\Factory\Form\EmploymentFormFactory'                                                    => __DIR__ . '/Learner/src/Factory/Form/EmploymentFormFactory.php',
    'Learner\Factory\Form\ForgotPasswordFormFactory'                                                => __DIR__ . '/Learner/src/Factory/Form/ForgotPasswordFormFactory.php',
    'Learner\Factory\Form\ImportFormFactory'                                                        => __DIR__ . '/Learner/src/Factory/Form/ImportFormFactory.php',
    'Learner\Factory\Form\PasswordFormFactory'                                                      => __DIR__ . '/Learner/src/Factory/Form/PasswordFormFactory.php',
    'Learner\Factory\Form\PhotoFormFactory'                                                         => __DIR__ . '/Learner/src/Factory/Form/PhotoFormFactory.php',
    'Learner\Factory\Form\RegisterFormFactory'                                                      => __DIR__ . '/Learner/src/Factory/Form/RegisterFormFactory.php',
    'Learner\Factory\Form\ResetPasswordFormFactory'                                                 => __DIR__ . '/Learner/src/Factory/Form/ResetPasswordFormFactory.php',
    'Learner\Factory\Form\DistributionFormFactory'                                                  => __DIR__ . '/Learner/src/Factory/Form/DistributionFormFactory.php',
    'Learner\Factory\Form\SettingsFormFactory'                                                      => __DIR__ . '/Learner/src/Factory/Form/SettingsFormFactory.php',
    'Learner\Factory\Service\AllActiveLearnersFactory'                                              => __DIR__ . '/Learner/src/Factory/Service/AllActiveLearnersFactory.php',
    'Learner\Factory\Service\AllInActiveLearnersFactory'                                            => __DIR__ . '/Learner/src/Factory/Service/AllInActiveLearnersFactory.php',
    'Learner\Factory\Service\AllLearnersFactory'                                                    => __DIR__ . '/Learner/src/Factory/Service/AllLearnersFactory.php',
    'Learner\Factory\Service\Delegator\OptionsDelegatorFactory'                                     => __DIR__ . '/Learner/src/Factory/Service/Delegator/OptionsDelegatorFactory.php',
    'Learner\Factory\Service\LearnerFactory'                                                        => __DIR__ . '/Learner/src/Factory/Service/LearnerFactory.php',
    'Learner\Factory\Service\LearnerRoleFactory'                                                    => __DIR__ . '/Learner/src/Factory/Service/LearnerRoleFactory.php',
    'Learner\Factory\Service\LearnerServiceFactory'                                                 => __DIR__ . '/Learner/src/Factory/Service/LearnerServiceFactory.php',
    'Learner\Factory\Service\OptionsServiceFactory'                                                 => __DIR__ . '/Learner/src/Factory/Service/OptionsServiceFactory.php',
    'Learner\Factory\View\Helper\LearnerViewHelperFactory'                                          => __DIR__ . '/Learner/src/Factory/View/Helper/LearnerViewHelperFactory.php',
    'Learner\Factory\View\Helper\ProfileHelperFactory'                                              => __DIR__ . '/Learner/src/Factory/View/Helper/ProfileHelperFactory.php',
    'Learner\Form\Employment'                                                                       => __DIR__ . '/Learner/src/Form/Employment.php',
    'Learner\Form\ImportForm'                                                                       => __DIR__ . '/Learner/src/Form/ImportForm.php',
    'Learner\Form\Initialiser'                                                                      => __DIR__ . '/Learner/src/Form/Initialiser.php',
    'Learner\Form\Learner'                                                                          => __DIR__ . '/Learner/src/Form/Learner.php',
    'Learner\Form\PhotoForm'                                                                        => __DIR__ . '/Learner/src/Form/PhotoForm.php',
    'Learner\Form\DistributionForm'                                                                 => __DIR__ . '/Learner/src/Form/DistributionForm.php',
    'Learner\Hydrator\AggregateHydrator'                                                            => __DIR__ . '/Learner/src/Hydrator/AggregateHydrator.php',
    'Learner\Hydrator\Employment'                                                                   => __DIR__ . '/Learner/src/Hydrator/Employment.php',
    'Learner\Hydrator\Learner'                                                                      => __DIR__ . '/Learner/src/Hydrator/Learner.php',
    'Learner\InputFilter\Employment'                                                                => __DIR__ . '/Learner/src/InputFilter/Employment.php',
    'Learner\InputFilter\Import'                                                                    => __DIR__ . '/Learner/src/InputFilter/Import.php',
    'Learner\InputFilter\Learner'                                                                   => __DIR__ . '/Learner/src/InputFilter/Learner.php',
    'Learner\InputFilter\PhotoInputFilter'                                                          => __DIR__ . '/Learner/src/InputFilter/PhotoInputFilter.php',
    'Learner\InputFilter\DistributionFilter'                                                        => __DIR__ . '/Learner/src/InputFilter/DistributionFilter.php',
    'Learner\Module'                                                                                => __DIR__ . '/Module.php',
    'Learner\Permissions\AssertPlatformIdMatches'                                                   => __DIR__ . '/Learner/src/Permissions/AssertPlatformIdMatches.php',
    'Learner\Service\LearnerService'                                                                => __DIR__ . '/Learner/src/Service/LearnerService.php',
    'Learner\Service\Options'                                                                       => __DIR__ . '/Learner/src/Service/Options.php',
    'Learner\Validator\EmailAddressExists'                                                          => __DIR__ . '/Learner/src/Validator/EmailAddressExists.php',
    'Learner\Validator\EmploymentIdExists'                                                          => __DIR__ . '/Learner/src/Validator/EmploymentIdExists.php',
    'Learner\Validator\HeaderRowExists'                                                             => __DIR__ . '/Learner/src/Validator/HeaderRowExists.php',
    'Learner\Validator\MobileNumberExists'                                                          => __DIR__ . '/Learner/src/Validator/MobileNumberExists.php',
    'Learner\View\Helper\Learner'                                                                   => __DIR__ . '/Learner/src/View/Helper/Learner.php',
    'Learner\View\Helper\Profile'                                                                   => __DIR__ . '/Learner/src/View/Helper/Profile.php',
    'Learner\View\Helper\ProfilePhoto'                                                              => __DIR__ . '/Learner/src/View/Helper/ProfilePhoto.php'
);
