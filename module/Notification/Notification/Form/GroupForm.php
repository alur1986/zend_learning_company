<?php

namespace Notification\Form;

use Savve\Form\AbstractForm;
use Savve\Form\Form;

class GroupForm extends AbstractForm
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

        // notification_id : hidden
        $this->add([
            'name' => 'notification_id',
            'type' => 'Hidden',
            'required' => true
        ]);

        // group_id : multicheckbox
        $this->add([
            'name' => 'group_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select Group',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0
            ]
        ]);
    }
}