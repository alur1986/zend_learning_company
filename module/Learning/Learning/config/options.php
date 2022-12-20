<?php

return [
    'learning_options' => [
        'activity_types' => [],
        'event_types' => [],
        'assessment_types' => [],
        'learning_types' => [],

        /**
         * File upload path location where to upload files for each learning activity
         */
        'file_upload_path' => PUBLIC_PATH . DIRECTORY_SEPARATOR . 'learning',

        /**
         * Base URI of the uploaded files
         */
        'file_base_uri' => '/learning/%activityId%/course/%filename%',
        'old_file_base_uri'=>'/courses/%activityId%/%filename%',

        /**
         * Backwards-compatible old file path of previous version of the module
         */
        'old_course_file_path' => PUBLIC_PATH . DIRECTORY_SEPARATOR . 'courses'
    ]
];