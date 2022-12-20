<?php

namespace Group\InputFilter;

use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class Group extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // group_id
        $this->add([
            'name' => 'group_id',
            'required' => true,
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

        // parent_id
        $this->add([
            'name' => 'parent_id',
            'required' => false,
            'allow_empty' => true,
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

        // name
        $this->add(
                [
                    'name' => 'name',
                    'required' => true,
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
                    ],
                    'validators' => [
                        [
                            'name' => 'NotEmpty',
                            'options' => [
                                'break_chain_on_failure' => true,
                                'message' => 'Please provide a group name'
                            ]
                        ],
                        [
                            'name' => 'Regex',
                            'options' => [
                                'break_chain_on_failure' => true,
                                'pattern' => '/[a-zA-Z0-9-\s]+$/',
                                'message' => "Group name can only contain alphanumeric, dash (-) and whitespace characters"
                            ]
                        ]
                    ]
                ]);

        // telephone
        $this->add([
            'name' => 'telephone',
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

        // fax
        $this->add([
            'name' => 'fax',
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

        // street_address
        $this->add([
            'name' => 'street_address',
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
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 125,
                        'messages' => [
                            StringLength::TOO_SHORT => "Postcode must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);

        // suburb
        $this->add([
            'name' => 'suburb',
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
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 125,
                        'messages' => [
                            StringLength::TOO_SHORT => "Postcode must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);

        // state
        $this->add([
            'name' => 'state',
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
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 125,
                        'messages' => [
                            StringLength::TOO_SHORT => "Postcode must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);

        // postcode
        $this->add([
            'name' => 'postcode',
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
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 12,
                        'messages' => [
                            StringLength::TOO_SHORT => "Postcode must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);

        // country
        $this->add([
            'name' => 'country',
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
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'messages' => [
                            StringLength::TOO_SHORT => "Country must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);
    }
}