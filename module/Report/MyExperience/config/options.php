<?php

return [
    'report' => [
        'myexperience' => [
            'templates' => [
                'default' => [
                    'title' => 'MyExperience xAPI Report - Default Template',
                    'description' => 'Default template for xAPI Reporting',
                    'config' => [
						[ 'learner_id' => 'Learner ID' ],
                        [ 'learner_name' => 'Learner Name' ],
						[ 'activity_title' => 'Activity Title' ],
						[ 'activity_verb' => 'Activity Verb' ],
                        [ 'activity_definition_name' => 'Activity Definition' ],
                        [ 'response' => 'Learner Response' ],
                        [ 'timestamp' => 'Timestamp' ],
                    ]
                ]
            ],

			'available_template_columns' => [
	            [ 'title' => 'Learner ID', 'key' => 'learner_id' ],
	            [ 'title' => 'Learner Name', 'key' => 'learner_name' ],
	            [ 'title' => 'Learner Email', 'key' => 'learner_email' ],
				[ 'title' => 'Learner CPD Identifier', 'key' => 'learner_cpd_id' ],
				[ 'title' => 'Learner CPD Number', 'key' => 'learner_cpd_number' ],
				[ 'title' => 'Activity IRI', 'key' => 'activity_iri' ],
	            [ 'title' => 'Activity Title', 'key' => 'activity_title' ],
				[ 'title' => 'Activity Verb', 'key' => 'activity_verb' ],
				[ 'title' => 'Activity Definition', 'key' => 'activity_definition_name' ],
				[ 'title' => 'Learning Activity (CPD) Points', 'key' => 'activity_cpd' ],
				[ 'title' => 'Definition Description', 'key' => 'activity_definition_description' ],
	            [ 'title' => 'Response Group', 'key' => 'response_group' ],
				[ 'title' => 'Group IRI', 'key' => 'group_iri' ],
				[ 'title' => 'Learner Response', 'key' => 'response' ],
				[ 'title' => 'Duration', 'key' => 'duration' ],
				[ 'title' => 'Timestamp', 'key' => 'timestamp' ],
			],

			'filter_variables' => [
				'activity_id',
				'activity_iri',
				'group_id',
				'learner_id',
				'completion_status',
				'learner_status'
            ]
        ]
    ]
];