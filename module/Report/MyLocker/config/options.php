<?php

return [
    'report' => [
        'mylocker' => [
            'templates' => [
                'default' => [
                    'title' => 'MyLocker Report Default Template',
                    'description' => 'Default template for the MyLocker Report',
                    'config' => [
                        [ 'resource_title' => 'Locker Title' ],
                        [ 'resource_filename' => 'Locker Filename' ],
                        [ 'resource_filetype' => 'Locker Filetype' ],
                        [ 'resource_created' => 'Locker Uploaded' ],
                        [ 'category_term' => 'Category' ],
                        [ 'verification_status' => 'Verification Status' ],
                    ]
                ]
            ],

			'available_template_columns' => [
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
                'show_from',
                'show_to',
                'all_dates',
                'verification_status'
            ]
        ]
    ]
];