<?php

namespace Company\InputFilter;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class Company extends InputFilter
{

    /**
     * Initialize the filter
     */
    public function __construct ()
    {
        $factory = new Factory();

        // company_id
        $this->add($factory->createInput([
            'name' => 'company_id',
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
                        'message' => 'Please enter a name for the company.'
                    ]
                ]
            ]
        ]));

        // telephone
        $this->add($factory->createInput([
            'name' => 'telephone',
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
                        'message' => 'Please enter a telephone number for the company.'
                    ]
                ]
            ]
        ]));

        // fax
        $this->add($factory->createInput([
            'name' => 'fax',
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

        // street_address
        $this->add($factory->createInput([
            'name' => 'street_address',
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
                        'message' => 'Please enter a street address for the company.'
                    ]
                ]
            ]
        ]));

        // suburb
        $this->add($factory->createInput([
            'name' => 'suburb',
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
                        'message' => 'Please enter a suburb for the company.'
                    ]
                ]
            ]
        ]));

        // postcode
        $this->add($factory->createInput([
            'name' => 'postcode',
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
                        'message' => 'Please enter a postcode for the company.'
                    ]
                ]
            ]
        ]));

        // state
        $this->add($factory->createInput([
            'name' => 'state',
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
                        'message' => 'Please enter a state for the company.'
                    ]
                ]
            ]
        ]));

        // country
        $this->add($factory->createInput([
            'name' => 'country',
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
                        'message' => 'Please enter a country for the company.'
                    ]
                ]
            ]
        ]));

        // website
        $this->add($factory->createInput([
            'name' => 'website',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ]
            ]
            ,
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please enter a website for the company.'
                    ]
                ]
            ]
        ]));

        // abn
        $this->add($factory->createInput([
            'name' => 'abn',
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