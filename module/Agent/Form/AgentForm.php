<?php

namespace Agent\Form;

use Savve\Form\AbstractForm;

class AgentForm extends AbstractForm
{

    /**
     * Constructor
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // agent_id : hidden
        $this->add([
            'name' => 'agent_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
        ]);

        // company_id : hidden
        $this->add([
            'name' => 'company_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
        ]);

        // name
        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'options' => [
                'label' => 'Agent / Agency name'
            ],
            'attributes' => [
                'placeholder' => 'Enter a name, eg Acme Pty Ltd'
            ]
        ]);

        // code
        $this->add([
            'name' => 'code',
            'type' => 'Text',
            'options' => [
                'label' => 'Code'
            ],
            'attributes' => [
                'placeholder' => 'Enter the agents code'
            ]
        ]);

        // password
        $this->add([
            'name' => 'password',
            'type' => 'Text',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => [
                'placeholder' => 'Enter a password'
            ]
        ]);

        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit'
            ],
            'options' => [
                'label' => 'Save'
            ]
        ]);

    }
}