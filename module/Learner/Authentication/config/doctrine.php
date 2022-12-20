<?php

return [
    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Savvecentral\Entity\Learner',
                'identity_property' => 'userId',
                'credential_property' => 'password',
                'credential_callable' => 'Authentication\Doctrine\Credential::setCredential'
            ]
        ]
    ],

    'doctrine_factories' => [
        'authenticationadapter' => 'Authentication\Factory\Authentication\AdapterFactory',
        'authenticationstorage' => 'Authentication\Factory\Authentication\StorageFactory'
    ]
];