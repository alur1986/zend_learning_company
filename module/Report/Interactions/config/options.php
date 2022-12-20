<?php

return [
    'report' => [
        'interactions' => [
            'templates' => [
                'default' => [
                    'title' => 'Interaction Report Default Template',
                    'description' => 'Default template for the Interaction report',
                    'config' => [
                        [ 'employment_id' => 'Employment ID' ],
                        [ 'fullname' => 'Full Name' ],
						[ 'email' => 'Email' ],
						[ 'title' => 'Activity Title' ],
                        [ 'identifier' => 'Identifier' ],
                        [ 'page_title' => 'Page Title' ],
                        [ 'response' => 'Response' ],
                        [ 'result' => 'Result' ],
                        [ 'last_accessed' => 'Last Accessed At' ]
                    ]
                ]
            ],

			'available_template_columns' => [
				[ 'title' => 'Employment ID', 'key' => 'employment_id' ],
	            [ 'title' => 'Full Name', 'key' => 'fullname' ],
	            [ 'title' => 'email', 'key' => 'Email' ],
	            [ 'title' => 'Activity Title', 'key' => 'title' ],
	            [ 'title' => 'Identifier', 'key' => 'identifier' ],
                    [ 'title' => 'Page Title', 'key' => 'page_title' ],
	            [ 'title' => 'Response', 'key' => 'response' ],
	            [ 'title' => 'Result', 'key' => 'result' ],
	            [ 'title' => 'Last Accessed At', 'key' => 'last_accessed' ]
			],

			'filter_variables' => [
                'generate_report_at'
            ]
        ]
    ]
];
