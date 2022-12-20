<?php

namespace Report\LearningProgressDetails\Form;

use Savve\Form\AbstractForm;

class FilterForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // filter_id : hidden
        $this->add([
            'name' => 'filter_id',
            'type' => 'Hidden',
            'required' => true
        ]);

        // title : text
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Title'
            ],
            'attributes' => [
                'placeholder' => 'Enter filter title'
            ]
        ]);

        // description : textarea
        $this->add([
            'name' => 'description',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Description'
            ],
            'attributes' => [
                'placeholder' => 'Enter filter description'
            ]
        ]);

        // activity_id : multicheckbox
        $this->add([
            'name' => 'activity_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select learning activities',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);

        // group_id : multicheckbox
        $this->add([
            'name' => 'group_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select groups',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);

        // learner_id : multicheckbox
        $this->add([
            'name' => 'learner_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select learners',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);

        // show_from : date
        $this->add([
            'name' => 'show_from',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Show from'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd)'
            ]
        ]);

        // show_to : date
        $this->add([
            'name' => 'show_to',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Show to'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd)'
            ]
        ]);

        // all_dates
        $this->add([
            'name' => 'all_dates',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Show all dates',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value' => 1
            ]
        ]);

        // tracking_status
        $this->add([
            'name' => 'tracking_status',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Learning Progress Status',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => [
                    [
                        'label' => 'Not attempted',
                        'value' => 'not-attempted'
                    ],
                    [
                        'label' => 'Incomplete',
                        'value' => 'incomplete'
                    ],
                    [
                        'label' => 'Completed',
                        'value' => 'completed'
                    ],
                    [
                        'label' => 'Passed',
                        'value' => 'passed'
                    ],
                    [
                        'label' => 'Failed',
                        'value' => 'failed'
                    ]
                ]
            ]
        ]);

        // learner_status
        $this->add([
            'name' => 'learner_status',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Learner Status',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => [
                    [
                        'label' => 'Active learners',
                        'value' => 'active'
                    ],
                    [
                        'label' => 'Inactive learners',
                        'value' => 'inactive'
                    ]
                ]
            ]
        ]);
    }
}
