<?php

return [
    'ldap' => [
        'production' => [
            'server1' => [
                'host' => '192.168.0.45',
                'accountDomainName' => 'savv-e.com',
                'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 3,
                'username' => 'ldap@savv-e.com',
                'password' => 'pass1',
                'baseDn' => 'OU=Sales,DC=foo,DC=net',
                'bindRequiresDn' => true
            ]
        ],
        'development' => [
            'server1' => [
                'host' => '192.168.0.203',
                'useStartTls' => false,
                'useSsl' => false,
                'accountDomainName' => 'savv-e.com',
                'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 4,
                'accountFilterFormat' => '(|(sn=person*)(givenname=person*))',
                'username' => 'ldap@savv-e.com',
                'password' => 'testing',
            //    'baseDn' => 'CN=Users,DC=savv-e,DC=com',
                'baseDn' => 'ou=SBSUsers,ou=Users,ou=MyBusiness,dc=savv-e,dc=com',
                'bindRequiresDn' => false
            ],
            'server2' => [
                'host' => '192.168.0.203',
                'useStartTls' => false,
                'useSsl' => false,
                'accountDomainName' => 'savv-e.com',
            //    'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 4,
                'accountFilterFormat' => '(&(objectCategory=person)(objectClass=user))',
                'username' => 'cn=ldap,dc=savv-e,dc=com',
                'password' => 'testing',
                'baseDn' => 'cn=Users,dc=savv-e,dc=com',
                'bindRequiresDn' => true
            ],
            'server3' => [
                'host' => '192.168.0.203',
                'useStartTls' => false,
                'useSsl' => false,
                'accountDomainName' => 'savv-e.com',
                'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 4,
                'username' => 'ldap@savv-e.com',
                'password' => 'testing',
                'baseDn' => 'OU=MyBusiness,DC=savv-e,DC=com',
                'bindRequiresDn' => false
            ]
        ],
        'testing' => [
             'server1' => [
                'host' => '192.168.0.45',
                'accountDomainName' => 'savv-e.com',
                'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 3,
                'username' => 'CN=user1,DC=savv-e,DC=com',
                'password' => 'pass1',
                'baseDn' => 'ou=SBSUsers,ou=Users,ou=MyBusiness,DC=savv-e,DC=com',
                'bindRequiresDn' => true
            ]
        ],
        'staging' => [
            'server1' => [
                'host' => '192.168.0.45',
                'accountDomainName' => 'savv-e.com',
                'accountDomainNameShort' => 'SAVV-E',
                'accountCanonicalForm' => 3,
                'username' => 'CN=user1,DC=savv-e,DC=com',
                'password' => 'pass1',
                'baseDn' => 'OU=Sales,DC=foo,DC=net',
                'bindRequiresDn' => true
            ]
        ]
    ]
];