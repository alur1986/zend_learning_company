<?php

namespace Notification\Form;

use Savve\Form\AbstractForm;

class LearnerForm extends AbstractForm
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

        // learner_id : multicheckbox
        $this->add([
            'name' => 'learner_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select Learner',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0
            ]
        ]);
    }
}