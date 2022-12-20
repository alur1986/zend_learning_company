<?php

namespace Authentication\InputFilter;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class Authentication extends InputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // identity
        $this->add([
            'name' => 'identity',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please enter the employment ID, email address or mobile number for your account.'
                    ]
                ]
            ]
        ]);

        // password
        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please enter your password.'
                    ]
                ]
            ]
        ]);

        // remember_me
        $this->add([
            'name' => 'remember_me',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]);

        // redirect_url
        $this->add([
            'name' => 'redirect_url',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Null'
                ]
            ]
        ]);
    }
}