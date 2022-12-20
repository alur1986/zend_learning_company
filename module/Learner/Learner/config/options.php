<?php

return [
    'learner_config' => [
        'password_token_expiry' => 60 * 60 * 3,
        'upload_path' => SITE_PATH . DIRECTORY_SEPARATOR . 'learner',
        'public_path' => PUBLIC_PATH . DIRECTORY_SEPARATOR . 'learner',
        'base_uri' => '/learner',
        'settings' => [
            'fields' => [
                'accepted_terms',
                'timezone',
                'locale',
                'profile_picture',
                'use_gravatar',
                'needs_change_password',
                'login_attempts'
            ]
        ],
        'profile_photo_placeholder' => null
    ]
];