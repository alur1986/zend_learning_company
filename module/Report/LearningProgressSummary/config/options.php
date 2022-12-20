<?php

return [
    'report' => [
        'learning-progress-summary' => [
            'templates' => [
                'default' => [
                    'title' => 'Learning Progress Summary Report Default Template',
                    'description' => 'Default template for the Learning Progress Summary Report',
                    'config' => [
                        [ 'activity_title' => 'Learning Activity Title' ],
                        [ 'num_learners_allocated' => 'Allocated Learners' ],
                        [ 'num_learners_not_attempted' => 'Not Attempted' ],
                        [ 'num_learners_attempted' => 'Attempted' ],
                        [ 'num_learners_incomplete' => 'Incomplete' ],
                        [ 'num_learners_failed' => 'Failed' ],
                        [ 'num_learners_passed' => 'Passed' ],
                        [ 'num_learners_completed' => 'Completed' ],
                        [ 'num_learners_expired' => 'Expired' ],
                    ]
                ]
            ],

			'available_template_columns' => [
	            [ 'key' => 'activity_type', 'title' => 'Learning Activity Type' ],
	            [ 'key' => 'activity_title', 'title' => 'Learning Activity Title' ],
	            [ 'key' => 'num_learners_allocated' , 'title' => 'Allocated Learners' ],
	            [ 'key' => 'num_learners_not_attempted' , 'title' => 'Not Attempted' ],
	            [ 'key' => 'num_learners_attempted' , 'title' => 'Attempted' ],
	            [ 'key' => 'num_learners_incomplete' , 'title' => 'Incomplete' ],
	            [ 'key' => 'num_learners_failed' , 'title' => 'Failed' ],
	            [ 'key' => 'num_learners_passed' , 'title' => 'Passed' ],
	            [ 'key' => 'num_learners_completed' , 'title' => 'Completed' ],
	            [ 'key' => 'num_learners_expired' , 'title' => 'Expired' ],
			]
        ]
    ]
];