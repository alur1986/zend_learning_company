<?php

return [
    'report' => [
        'xapi-interactions' => [
            'templates' => [
                'default' => [
                    'title' => 'xAPI Interactions Report Default Template',
                    'description' => 'Default template for the Interactions Report',
                    'config' => [
                        [ 'group_name' => 'Learner Group' ],
                        [ 'learner_id' => 'Learner ID' ],
                        [ 'employment_id' => 'Employment ID' ],
                        [ 'learner_name' => 'Learner Name' ],
                        [ 'employment_location' => 'Employment Location' ],
                        [ 'employment_position' => 'Employment Position' ],
                        [ 'employment_type' => 'Employment Type' ],
                        [ 'employment_cost_centre' => 'Employment Cost Centre' ],
                        [ 'activity_title' => 'Activity Title' ],
                        [ 'activity_version' => 'Activity Version' ],
                        [ 'xapi_activity_definition' => 'xAPI Activity Page Title' ],
                        [ 'xapi_activity_verb' => 'xAPI Activity Verb' ],
                        [ 'xapi_question_assessment' => 'Assessment' ],
                        [ 'xapi_question_type' => 'Activity Definition' ],
                        [ 'xapi_question_title' => 'Activity Question' ],
                        [ 'xapi_question_answered_correct' => 'Answered Correct' ],
                        [ 'xapi_question_options' => 'Activity Question Options' ],
                        [ 'xapi_activity_timestamp' => 'Timestamp' ]
                    ]
                ]
            ],

            'available_template_columns' => [
                [ 'title' => 'Activity Type', 'key' => 'activity_type' ],
                [ 'title' => 'Activity Title', 'key' => 'activity_title' ],
                [ 'title' => 'Activity Category', 'key' => 'activity_category' ],
                [ 'title' => 'Activity Keywords', 'key' => 'activity_keyword' ],
                [ 'title' => 'Activity Code', 'key' => 'activity_code' ],
                [ 'title' => 'Activity Version', 'key' => 'activity_version' ],
                [ 'title' => 'Activity CPD', 'key' => 'activity_cpd' ],
                [ 'title' => 'Activity Course Duration', 'key' => 'activity_duration' ],
                [ 'title' => 'Activity Direct Cost', 'key' => 'activity_direct_cost' ],
                [ 'title' => 'Activity Indirect Cost', 'key' => 'activity_indirect_cost' ],
                [ 'title' => 'Learner ID', 'key' => 'learner_id' ],
                [ 'title' => 'Learner First Name', 'key' => 'learner_first_name' ],
                [ 'title' => 'Learner Last Name', 'key' => 'learner_last_name' ],
                [ 'title' => 'Learner Name', 'key' => 'learner_name' ],
                [ 'title' => 'Learner Telephone', 'key' => 'learner_telephone' ],
                [ 'title' => 'Learner Gender', 'key' => 'learner_gender' ],
                [ 'title' => 'Learner Postcode', 'key' => 'learner_postcode' ],
                [ 'title' => 'Learner Email', 'key' => 'learner_email' ],
                [ 'title' => 'Learner CPD Identifier', 'key' => 'learner_cpd_id' ],
                [ 'title' => 'Learner CPD Number', 'key' => 'learner_cpd_number' ],
                [ 'title' => 'Learner Agent Code', 'key' => 'agent_code' ],
                [ 'title' => 'Learner Status', 'key' => 'learner_status' ],
                [ 'title' => 'Learner Group', 'key' => 'group_name' ],
                [ 'title' => 'Employment ID', 'key' => 'employment_id' ],
                [ 'title' => 'Employment Type', 'key' => 'employment_type' ],
                [ 'title' => 'Employment Manager', 'key' => 'employment_manager' ],
                [ 'title' => 'Employment Position', 'key' => 'employment_position' ],
                [ 'title' => 'Employment Location', 'key' => 'employment_location' ],
                [ 'title' => 'Employment Start Date', 'key' => 'employment_start_date' ],
                [ 'title' => 'Employment End Date', 'key' => 'employment_end_date' ],
                [ 'title' => 'Employment Cost Centre', 'key' => 'employment_cost_centre' ],
                [ 'title' => 'xAPI Activity Page Title', 'key' => 'xapi_activity_definition' ],
                [ 'title' => 'xAPI Action Verb', 'key' => 'xapi_activity_verb' ],
                [ 'title' => 'Assessment', 'key' => 'xapi_question_assessment' ],
                [ 'title' => 'Activity Definition', 'key' => 'xapi_question_type' ],
                [ 'title' => 'Activity Question', 'key' => 'xapi_question_title' ],
                [ 'title' => 'Answered Correct', 'key' => 'xapi_question_answered_correct' ],
                [ 'title' => 'Activity Question Options', 'key' => 'xapi_question_options' ],
                [ 'title' => 'Activity Question Duration', 'key' => 'xapi_activity_duration' ],
                [ 'title' => 'Activity Question Score', 'key' => 'xapi_activity_score' ],
                [ 'title' => 'Timestamp', 'key' => 'xapi_activity_timestamp' ]   
            ]
        ]
    ]
];
