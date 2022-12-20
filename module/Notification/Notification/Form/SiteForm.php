<?php

namespace Notification\Form;

use Savve\Form\AbstractForm;
use Savve\Form\Form;

class SiteForm extends AbstractForm
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

        // send_to_all : checkbox
        $this->add([
            'name' => 'send_to_all',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Send to all',
                'use_hidden_element' => true,
                'checked_value' => 'yes',
                'unchecked_value' => 'no'
            ]
        ]);
    }
}