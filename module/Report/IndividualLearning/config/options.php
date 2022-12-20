<?php

return [
    'report' => [
        'individual-learning' => [
            'templates' => [
                'default' => [
                    'title' => 'Individual Learning Report Default Template',
                    'description' => 'Default template for the Individual Learning Report',
                    'config' => [
                        [ 'activity_title' => 'Activity Title' ],
                        [ 'activity_type' => 'Activity Type' ],
                        [ 'learner_name' => 'Learner Full Name' ],
                        [ 'learner_email' => 'Learner Email' ],
                        [ 'employment_id' => 'Employment ID' ],
                        [ 'employment_cost_centre' => 'Cost Centre' ],
                        [ 'event_start_date' => 'Start Date' ],
                        [ 'event_end_date' => 'End Date' ],
                        [ 'tracking_score' => 'Score' ],
                        [ 'tracking_status' => 'Status' ],
                    ]
                ]
            ],

			'available_template_columns' => [
	            [ 'title' => 'Learning Activity Type', 'key' => 'activity_type' ],
	            [ 'title' => 'Learning Activity Title', 'key' => 'activity_title' ],
	            [ 'title' => 'Learning Activity Category', 'key' => 'activity_category' ],
	            [ 'title' => 'Learning Activity Keywords', 'key' => 'activity_keyword' ],
	            [ 'title' => 'Learning Activity Code', 'key' => 'activity_code' ],
	            [ 'title' => 'Learning Activity Version', 'key' => 'activity_version' ],
	            [ 'title' => 'Learning Activity Continuing Profession Development (CPD) Points', 'key' => 'activity_cpd' ],
	            [ 'title' => 'Learning Activity Course Duration', 'key' => 'activity_duration' ],
	            [ 'title' => 'Learning Activity Direct Cost', 'key' => 'activity_direct_cost' ],
	            [ 'title' => 'Learning Activity Indirect Cost', 'key' => 'activity_indirect_cost' ],
	            [ 'title' => 'Learner First Name', 'key' => 'learner_first_name' ],
	            [ 'title' => 'Learner Last Name', 'key' => 'learner_last_name' ],
	            [ 'title' => 'Learner Full Name', 'key' => 'learner_name' ],
	            [ 'title' => 'Learner Telephone', 'key' => 'learner_telephone' ],
	            [ 'title' => 'Learner Gender', 'key' => 'learner_gender' ],
	            [ 'title' => 'Learner Postcode', 'key' => 'learner_postcode' ],
	            [ 'title' => 'Learner Email', 'key' => 'learner_email' ],
	            [ 'title' => 'Employment ID', 'key' => 'employment_id' ],
	            [ 'title' => 'Employment Type', 'key' => 'employment_type' ],
	            [ 'title' => 'Employment Manager', 'key' => 'employment_manager' ],
	            [ 'title' => 'Employment Position', 'key' => 'employment_position' ],
	            [ 'title' => 'Employment Start Date', 'key' => 'employment_start_date' ],
	            [ 'title' => 'Employment End Date', 'key' => 'employment_end_date' ],
	            [ 'title' => 'Employment Cost Centre', 'key' => 'employment_cost_centre' ],
	            [ 'title' => 'Learning Status', 'key' => 'tracking_status' ],
	            [ 'title' => 'Learning Score', 'key' => 'tracking_score' ],
	            [ 'title' => 'Date Started', 'key' => 'tracking_started' ],
	            [ 'title' => 'Completion Date', 'key' => 'tracking_completed' ],
	            [ 'title' => 'Distribution Date', 'key' => 'distribution_date' ],
	            [ 'title' => 'Expiry Date', 'key' => 'expiry_date' ],
			],

			'filter_variables' => [
                'site_id',
                'activity_id',
                'group_id',
                'show_from',
                'show_to',
                'all_dates',
                'tracking_status',
                'learner_status',
            ]
        ]
    ]
];