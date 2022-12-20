<?php

return [
    'report' => [
        'individual-locker' => [
            'templates' => [
                'default' => [
                    'title' => 'Individual Locker Report Default Template',
                    'description' => 'Default template for the Individual Locker Report',
                    'config' => [
                        [ 'learner_name' => 'Learner Full Name' ],
                        [ 'learner_email' => 'Learner Email' ],
                        [ 'resource_title' => 'Locker Title' ],
                        [ 'resource_created' => 'Locker Uploaded' ],
                        [ 'category_term' => 'Category' ],
                        [ 'verification_status' => 'Verification Status' ],
                    ]
                ]
            ],

			'available_template_columns' => [
	            [ 'title' => 'Learner First Name', 'key' => 'learner_first_name' ],
	            [ 'title' => 'Learner Last Name', 'key' => 'learner_last_name' ],
	            [ 'title' => 'Learner Full Name', 'key' => 'learner_name' ],
	            [ 'title' => 'Learner Email', 'key' => 'learner_email' ],
	            [ 'title' => 'Learner Telephone', 'key' => 'learner_telephone' ],
	            [ 'title' => 'Learner Mobile Number', 'key' => 'learner_mobile_number' ],
	            [ 'title' => 'Employment ID', 'key' => 'employment_id' ],
	            [ 'title' => 'Employment Type', 'key' => 'employment_type' ],
	            [ 'title' => 'Employment Position', 'key' => 'employment_position' ],
	            [ 'title' => 'Employment Start Date', 'key' => 'employment_start_date' ],
	            [ 'title' => 'Employment End Date', 'key' => 'employment_end_date' ],
	            [ 'title' => 'Locker Title', 'key' => 'resource_title' ],
	            [ 'title' => 'Locker Filename', 'key' => 'resource_filename' ],
	            [ 'title' => 'Locker Filetype', 'key' => 'resource_filetype' ],
	            [ 'title' => 'Locker Uploaded', 'key' => 'resource_created' ],
	            [ 'title' => 'Verification Status', 'key' => 'verification_status' ],
	            [ 'title' => 'Category', 'key' => 'category_term' ],
			],

			'filter_variables' => [
                'site_id',
                'category_id',
                'group_id',
                'learner_id',
                'show_from',
                'show_to',
                'all_dates',
                'verification_status',
                'learner_status'
            ]
        ]
    ]
];