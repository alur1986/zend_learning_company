<?php

namespace Elucidat\InputFilter;

use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class Elucidat extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // id
        $this->add([
            'name' => 'id',
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
        // create new account in elucidat
        $this->add([
            'name' => 'create_matching_account',
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

        // site_id
        $this->add([
                       'name' => 'site_id',
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

        // site_id
        $this->add([
                       'name' => 'status',
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

        // elucidat_customer_code
        $this->add([
            'name' => 'elucidat_customer_code',
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
        // elucidat_public_key
        $this->add([
                       'name' => 'elucidat_public_key',
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


        // company_name
        $this->add(
                [
                    'name' => 'company_name',
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
                                'message' => 'Please provide a company name'
                            ]
                        ],
                        [
                            'name' => 'Regex',
                            'options' => [
                                'break_chain_on_failure' => true,
                                'pattern' => '/[a-zA-Z0-9-\s]+$/',
                                'message' => "Elucidat name can only contain alphanumeric, dash (-) and whitespace characters"
                            ]
                        ]
                    ]
                ]);

        // email
        $this->add([
                       'name' => 'company_email',
                       'required' => true,
                       'filters' => [
                           [
                               'name' => 'StringTrim'
                           ],
                           [
                               'name' => 'StripTags'
                           ],
                           [
                               'name' => 'Null'
                           ]
                       ],
                       'validators' => [
                           [
                               'name' => 'NotEmpty',
                               'options' => [
                                   'message' => 'Please provide a valid email address.',
                                   'break_chain_on_failure' => true
                               ]
                           ],
                           [
                               'name' => 'EmailAddress',
                               'options' => [
                                   'message' => "The email address entered is not valid. Please enter a valid email address.",
                                   'break_chain_on_failure' => true
                               ]
                           ],
                       ]
                   ]);


        // first_name
        $this->add(
            [
                'name' => 'first_name',
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
                            'message' => 'Please provide a first name'
                        ]
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'break_chain_on_failure' => true,
                            'pattern' => '/[a-zA-Z0-9-\s]+$/',
                            'message' => "Elucidat name can only contain alphanumeric, dash (-) and whitespace characters"
                        ]
                    ]
                ]
            ]);

        // last_name
        $this->add(
            [
                'name' => 'last_name',
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
                            'message' => 'Please provide a last name'
                        ]
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'break_chain_on_failure' => true,
                            'pattern' => '/[a-zA-Z0-9-\s]+$/',
                            'message' => "Elucidat name can only contain alphanumeric, dash (-) and whitespace characters"
                        ]
                    ]
                ]
            ]);

        // telephone
        $this->add([
            'name' => 'telephone',
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

        // street_address
        $this->add([
            'name' => 'address1',
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


        // street_address
        $this->add([
                       'name' => 'address2',
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
        // postcode
        $this->add([
            'name' => 'city',
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
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 20,
                        'messages' => [
                            StringLength::TOO_SHORT => "City must be at least %min% characters in length."
                        ]
                    ]
                ]
            ]
        ]);

        // country
        $this->add([
            'name' => 'country',
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