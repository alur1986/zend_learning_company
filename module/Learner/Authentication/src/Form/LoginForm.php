<?php

namespace Authentication\Form;

use Savve\Form\AbstractForm;

class LoginForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct ($name = null, array $options = [])
    {
        parent::__construct($name, $options);

        // identity : text
        $this->add([
            'name' => 'identity',
            'type' => 'Text',
            'options' => [
                'label' => 'Email, Mobile, Employment ID'
            ],
            'attributes' => [
                'placeholder' => "Email, Mobile, Employment ID"
            ]
        ]);

        // password : text
        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => [
                'placeholder' => "Password"
            ]
        ]);

        // remember_me : checkbox
        $this->add([
            'name' => 'remember_me',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Remember me',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'checked' => 0
            ],
            'attributes' => [
                'title' => 'Rememeber my login details'
            ]
        ]);

        // password_token : hidden
        $this->add([
            'name' => 'password_token',
            'type' => 'Hidden',
            'required' => true
        ]);

        // redirect_url : hidden
        $this->add([
            'name' => 'redirect_url',
            'type' => 'Hidden',
            'required' => false
        ]);

        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Login'
            ],
            'options' => [
                'label' => 'Login'
            ]
        ]);
    }
}
