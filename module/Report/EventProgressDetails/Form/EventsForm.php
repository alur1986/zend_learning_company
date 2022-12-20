<?php

namespace Report\EventProgressDetails\Form;

use Savve\Form\AbstractForm;

class EventsForm extends AbstractForm
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