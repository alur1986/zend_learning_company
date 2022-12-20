<?php

namespace Notification\Form;

use Savve\Form\AbstractForm;
use Savve\Form\Form;

class NotificationForm extends AbstractForm
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

        // subject
        $this->add([
            'name' => 'subject',
            'type' => 'Text',
            'options' => [
                'label' => 'Subject'
            ],
            'attributes' => [
                'placeholder' => 'Enter subject'
            ]
        ]);

        // message
        $this->add([
            'name' => 'message',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Message'
            ],
            'attributes' => [
                'placeholder' => 'Enter message'
            ]
        ]);

        // sender_name
        $this->add([
            'name' => 'sender_name',
            'type' => 'Text',
            'options' => [
                'label' => 'Sender name'
            ],
            'attributes' => [
                'placeholder' => 'Enter sender name'
            ]
        ]);

        // sender_email
        $this->add([
            'name' => 'sender_email',
            'type' => 'Text',
            'options' => [
                'label' => 'Sender email'
            ],
            'attributes' => [
                'placeholder' => 'Enter sender email'
            ]
        ]);

        // active_from
        $default = new \DateTime(date('Y-m-d H:i'));
        $this->add([
            'name' => 'active_from',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Active from',
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)',
                'value' => $default
            ]
        ]);

        // active_to
        $this->add([
            'name' => 'active_to',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Active to',
                'format' => 'Y-m-d H:i'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)'
            ]
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