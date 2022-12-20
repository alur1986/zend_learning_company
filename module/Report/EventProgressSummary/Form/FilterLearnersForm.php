<?php

namespace Report\EventProgressSummary\Form;

use Savve\Form\AbstractForm;

class FilterLearnersForm extends AbstractForm
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
    }
}