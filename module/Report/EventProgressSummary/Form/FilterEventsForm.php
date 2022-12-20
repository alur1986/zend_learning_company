<?php

namespace Report\EventProgressSummary\Form;

use Savve\Form\AbstractForm;

class FilterEventsForm extends AbstractForm
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

        // event_id : multicheckbox
        $this->add([
            'name' => 'event_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select events',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);
    }
}