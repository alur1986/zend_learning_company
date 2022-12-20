<?php

namespace Report\IndividualLearning\Form;

use Savve\Form\AbstractForm;

class ActivitiesForm extends AbstractForm
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