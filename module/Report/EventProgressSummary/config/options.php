<?php

return [
    'report' => [
        'event-progress-summary' => [
            'templates' => [
                'default' => [
                    'title' => 'Event Progress Summary Report Default Template',
                    'description' => 'Default template for the Event Progress Summary Report',
                    'config' => [
                        [ 'event_title' => 'Event Title' ],
                        [ 'activity_title' => 'Learning Activity Title' ],
                        [ 'activity_type' => 'Learning Activity Type' ],
                        [ 'event_start_date' => 'Start Date' ],
                        [ 'event_end_date' => 'End Date' ],
                        [ 'num_assessors' => 'No. of Assessors' ],
                        [ 'num_facilitators' => 'No. of Facilitators' ],
                        [ 'num_vendors' => 'No. of Vendors' ],
                        [ 'num_venues' => 'No. of Venues' ],
                        [ 'num_learners_allocated' => 'Allocated Learners' ],
                        [ 'num_learners_not_attempted' => 'Not Attempted' ],
                        [ 'num_learners_attempted' => 'Attempted' ],
                        [ 'num_learners_completed' => 'Completed' ],
                        [ 'num_learners_incomplete' => 'Incomplete' ],
                        [ 'num_learners_passed' => 'Passed' ],
                        [ 'num_learners_failed' => 'Failed' ],
                    ]
                ]
            ],

			'available_template_columns' => [
	            [ 'key' => 'activity_title' , 'title' => 'Learning Activity Title' ],
	            [ 'key' => 'activity_type' , 'title' => 'Learning Activity Type' ],
	            [ 'key' => 'event_title' , 'title' => 'Event Title' ],
	            [ 'key' => 'event_start_date' , 'title' => 'Event Start Date' ],
	            [ 'key' => 'event_end_date' , 'title' => 'Event End Date' ],
	            [ 'key' => 'num_assessors' , 'title' => 'No. of Assessors' ],
	            [ 'key' => 'num_facilitators' , 'title' => 'No. of Facilitators' ],
	            [ 'key' => 'num_vendors' , 'title' => 'No. of Vendors' ],
	            [ 'key' => 'num_venues' , 'title' => 'No. of Venues' ],
	            [ 'key' => 'num_learners_allocated' , 'title' => 'Allocated Learners' ],
	            [ 'key' => 'num_learners_not_attempted' , 'title' => 'Not Attempted' ],
	            [ 'key' => 'num_learners_attempted' , 'title' => 'Attempted' ],
	            [ 'key' => 'num_learners_completed' , 'title' => 'Completed' ],
	            [ 'key' => 'num_learners_incomplete' , 'title' => 'Incomplete' ],
	            [ 'key' => 'num_learners_failed' , 'title' => 'Failed' ],
	            [ 'key' => 'num_learners_passed' , 'title' => 'Passed' ],
			],

			'filter_variables' => [
                'site_id',
                'activity_id',
                'event_id',
                'group_id',
                'show_from',
                'show_to',
                'all_dates',
                'tracking_status',
                'learner_status'
            ]
        ]
    ]
];