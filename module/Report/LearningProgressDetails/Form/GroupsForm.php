<?php

namespace Report\LearningProgressDetails\Form;

use Savve\Form\AbstractForm;

class GroupsForm extends AbstractForm
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
    }
}