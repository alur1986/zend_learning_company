<?php

namespace Report\EventProgressSummary\Form;

use Savve\Form\AbstractForm;

class FilterActivitiesForm extends AbstractForm
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
    }
}