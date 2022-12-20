<?php

namespace Learner\Form;

use Savve\Form\AbstractForm;

class Employment extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, $options = [])
    {
        parent::__construct($name, $options);

        // user_id : hidden
        $this->add([
            'name' => 'user_id',
            'type' => 'Hidden'
        ]);

        // employment_id : text
        $this->add([
            'name' => 'employment_id',
            'type' => 'Text',
            'options' => [
                'label' => 'Employment ID'
            ],
            'attributes' => [
                'placeholder' => 'Enter employment ID'
            ]
        ]);

        // location
        $this->add([
            'name' => 'location',
            'type' => 'Text',
            'options' => [
                'label' => 'Location'
            ]
        ]);

        // position
        $this->add([
            'name' => 'position',
            'type' => 'Text',
            'options' => [
                'label' => 'Position'
            ],
            'attributes' => [
                'placeholder' => 'Enter employment position'
            ]
        ]);

        // employment_type : select
        $this->add([
            'name' => 'employment_type',
            'type' => 'Select',
            'options' => [
                'label' => 'Employment type',
                'empty_option' => 'Select employment type',
                'value_options' => [
                    'part-time' => 'Part-time',
                    'casual' => 'Casual',
                    'full-time' => 'Full-time',
                    'contract' => 'Contract',
                    'temporary' => 'Temporary',
                    'shift-work' => 'Shift-work',
                    'paternity-leave' => 'Paternity-Leave',
		    'maternity-leave' => 'Maternity-Leave',
                    'extended-leave' => 'Extended-Leave'
                ]
            ]
        ]);

        // start_date : datetime
        $this->add([
            'name' => 'start_date',
            'type' => 'Text',
            'options' => [
                'label' => 'Employment start date',
                'description' => "Employment start date in yyyy-mm-dd format",
            ],
            'attributes' => [
                'placeholder' => 'Enter employment start date',
                'data-datepicker' => 1
            ]
        ]);

        // end_date : datetime
        $this->add([
            'name' => 'end_date',
            'type' => 'Text',
            'options' => [
                'label' => 'Employment end date',
                'description' => "Employment end date in yyyy-mm-dd format",
            ],
            'attributes' => [
                'placeholder' => 'Enter employment end date',
                'data-datepicker' => 1
            ]
        ]);

        // cost_centre
        $this->add([
            'name' => 'cost_centre',
            'type' => 'Text',
            'options' => [
                'label' => 'Cost centre'
            ],
            'attributes' => [
                'placeholder' => 'Enter cost centre'
            ]
        ]);

        // manager
        $this->add([
            'name' => 'manager',
            'type' => 'Text',
            'options' => [
                'label' => 'Manager'
            ],
            'attributes' => [
                'placeholder' => 'Enter manager\'s name'
            ]
        ]);

        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit'
            ],
            'options' => [
                'label' => 'Save'
            ]
        ]);
    }
}
