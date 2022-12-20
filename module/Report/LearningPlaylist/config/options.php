<?php

return [
    'report' => [
        'learning-playlist' => [
            'templates' => [
                'default' => [
                    'title' => 'Learning Playlist Report Default Template',
                    'description' => 'Default template for the Learning Playlist Details Report',
                    'config' => [
						[ 'plan_title' => 'Learning Playlist Title' ],
                        [ 'activity_title' => 'Learning Activity Title' ],
                        [ 'activity_type' => 'Learning Activity Type' ],
                        [ 'learner_name' => 'Learner Name' ],
                        [ 'learner_email' => 'Learner Email' ],
                        [ 'employment_id' => 'Employment ID' ],
                        [ 'tracking_status' => 'Status' ],
                        [ 'tracking_score' => 'Score' ],
                        [ 'tracking_started' => 'Date Started' ],
                        [ 'tracking_completed' => 'Completion Date' ],
                        [ 'distribution_date' => 'Distribution Date' ]
                    ]
                ]
            ],

			'available_template_columns' => [
				[ 'title' => 'Learning Playlist Title', 'key' => 'plan_title' ],
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
                [ 'title' => 'Agency Code', 'key' => 'agency_code' ],
                [ 'title' => 'Agency Name', 'key' => 'agency_name' ],
                [ 'title' => 'Playlist Completion Status', 'key' => 'completion_status' ],
                [ 'title' => 'Completion Count', 'key' => 'completion_count' ],
	            [ 'title' => 'Learner First Name', 'key' => 'learner_first_name' ],
	            [ 'title' => 'Learner Last Name', 'key' => 'learner_last_name' ],
	            [ 'title' => 'Learner Full Name', 'key' => 'learner_name' ],
	            [ 'title' => 'Learner Telephone', 'key' => 'learner_telephone' ],
	            [ 'title' => 'Learner Gender', 'key' => 'learner_gender' ],
	            [ 'title' => 'Learner Postcode', 'key' => 'learner_postcode' ],
	            [ 'title' => 'Learner Email', 'key' => 'learner_email' ],
				[ 'title' => 'Learner Status', 'key' => 'learner_status' ],
	            [ 'title' => 'Employment ID', 'key' => 'employment_id' ],
	            [ 'title' => 'Employment Type', 'key' => 'employment_type' ],
	            [ 'title' => 'Employment Manager', 'key' => 'employment_manager' ],
	            [ 'title' => 'Employment Position', 'key' => 'employment_position' ],
	            [ 'title' => 'Employment Location', 'key' => 'employment_location' ],
	            [ 'title' => 'Employment Start Date', 'key' => 'employment_start_date' ],
	            [ 'title' => 'Employment End Date', 'key' => 'employment_end_date' ],
	            [ 'title' => 'Learning Status', 'key' => 'tracking_status' ],
	            [ 'title' => 'Learning Score', 'key' => 'tracking_score' ],
	            [ 'title' => 'Date Started', 'key' => 'tracking_started' ],
	            [ 'title' => 'Completion Date', 'key' => 'tracking_completed' ],
	            [ 'title' => 'Distribution Date', 'key' => 'distribution_date' ],
	            [ 'title' => 'Expiry Date', 'key' => 'expiry_date' ],
			]
        ]
    ]
];