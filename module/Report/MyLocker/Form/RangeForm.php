<?php

namespace Report\MyLocker\Form;

use Savve\Form\AbstractForm;

class RangeForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, array $options = [])
    {
        parent::__construct($name, $options);

        // show_from : date
        $this->add([
                       'name' => 'show_from',
                       'type' => 'DateTime',
                       'options' => [
                           'label' => 'Show from',
                           'format' => 'Y-m-d'
                       ],
                       'attributes' => [
                           'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)'
                       ]
                   ]);

        // show_to : date
        $this->add([
                       'name' => 'show_to',
                       'type' => 'DateTime',
                       'options' => [
                           'label' => 'Show to',
                           'format' => 'Y-m-d'
                       ],
                       'attributes' => [
                           'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)'
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

        // verification_status
        $this->add([
            'name' => 'verification_status',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Verification Status',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => [
                    [
                        'label' => 'Valid',
                        'value' => 'valid'
                    ],
                    [
                        'label' => 'Invalid',
                        'value' => 'invalid'
                    ],
                    [
                        'label' => 'Sighted',
                        'value' => 'sighted'
                    ],
                    [
                        'label' => 'Pending',
                        'value' => 'pending'
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