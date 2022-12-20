<?php

namespace Agent\InputFilter;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class Agent extends InputFilter
{

    /**
     * Initialize the filter
     */
    public function __construct ()
    {
        $factory = new Factory();

        // agent_id
        $this->add($factory->createInput([
            'name' => 'agent_id',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]));

        // site_id
        $this->add($factory->createInput([
            'name' => 'site_id',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]));

        // name
        $this->add($factory->createInput([
            'name' => 'name',
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
                        'message' => 'Please enter a name for the agent / agency.'
                    ]
                ]
            ]
        ]));

        // code
        $this->add($factory->createInput([
            'name' => 'code',
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
                        'message' => 'Please enter a code number for the agent / agency.'
                    ]
                ]
            ]
        ]));

        // password
        $this->add($factory->createInput([
            'name' => 'password',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ]
        ]));
    }
}