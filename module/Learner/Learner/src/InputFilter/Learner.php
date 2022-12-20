<?php

namespace Learner\InputFilter;

use Zend\InputFilter\Factory;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\Validator\EmailAddress;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class Learner extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // user_id
        $this->add([
            'name' => 'user_id',
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
            ]
        ]);

        // first_name
        $this->add([
            'name' => 'first_name',
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
                        'message' => "Please provide your first name.",
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 125,
                        'message' => "First name must be between %min% and %max% characters in length."
                    ]
                ]
            ]
        ]);

        // last_name
        $this->add([
            'name' => 'last_name',
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
                        'message' => "Please provide your last name.",
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 125,
                        'message' => "Last name must be between %min% and %max% characters in length."
                    ]
                ]
            ]
        ]);

        // email
        $this->add([
            'name' => 'email',
            'required' => false,
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
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => "The email address entered is not valid. Please enter a valid email address.",
                        'break_chain_on_failure' => true
                    ]
                ],
            ]
        ]);

        // telephone
        $this->add([
            'name' => 'telephone',
            'required' => false,
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
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 32,
                        'message' => "Telephone number must be between %min% and %max% characters in length."
                    ]
                ]
            ]
        ]);

        // mobile_number
        $this->add([
            'name' => 'mobile_number',
            'required' => false,
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
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 8,
                        'max' => 16,
                        'message' => "Mobile number must be between %min% and %max% characters in length.",
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);

        // street_address
        $this->add([
            'name' => 'street_address',
            'required' => false,
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
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 0,
                        'max' => 125,
                        'message' => "Street address must be between %min% and %max% characters in length."
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
                        'message' => 'Please provide a suburb'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max' => 125,
                        'message' => "Suburb must be between %min% and %max% characters in length."
                    ]
                ]
            ]
        ]);

        // group
        $this->add([
            'name' => 'group_id',
            'required' => false,
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
                        'message' => "Please select a group."
                    ]
                ]
            ]
        ]);

        // cpd_id
        $this->add([
            'name' => 'cpd_id',
            'required' => false,
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
                        'message' => "Please provide a CPD Identifier."
                    ]
                ]
            ]
        ]);

        // cpd_number
        $this->add([
            'name' => 'cpd_number',
            'required' => false,
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
                        'message' => "Please provide a CPD Number."
                    ]
                ]
            ]
        ]);

        // referrer
        $this->add([
            'name' => 'referrer',
            'required' => false,
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
                        'message' => "Please selecte a Referrer."
                    ]
                ]
            ]
        ]);

        // note
        $this->add([
            'name' => 'note',
            'required' => false,
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
                        'message' => "Please provide a note"
                    ]
                ]
            ]
        ]);

        // subscription
        $this->add([
            'name' => 'subscription',
            'required' => false,
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
            ]
        ]);

        // state
        $this->add([
            'name' => 'state',
            'required' => false,
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
                        'message' => "Please provide a state."
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
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 4,
                        'max' => 12,
                        'message' => "Postcode must be between %min% and %max% characters in length."
                    ]
                ],
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => "Please provide a postcode."
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
                        'message' => "Please provide a country."
                    ]
                ]
            ]
        ]);

        // gender
        $this->add([
            'name' => 'gender',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ],
                [
                    'name' => 'Null'
                ]
            ]
        ]);

        // timezone
        $this->add([
            'name' => 'timezone',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Null'
                ]
            ]
        ]);

        // locale
        $this->add([
            'name' => 'locale',
            'required' => false,
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
            ]
        ]);

        // new_password
        $this->add([
            'name' => 'new_password',
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
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Please provide a password.'
                        ],
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 32,
                        'message' => "Password must be between %min% and %max% characters in length."
                    ]
                ]
            ]
        ]);

        // confirm_password
        $this->add([
            'name' => 'confirm_password',
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
                        'message' => 'Please confirm password.',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'new_password',
                        'message' => "Your passwords do not match."
                    ]
                ]
            ]
        ]);

        // identity
        $this->add([
            'name' => 'identity',
            'required' => false,
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
                        'message' => 'Please provide a valid email address or mobile number.'
                    ]
                ]
            ]
        ]);

        // employment_id
        $this->add([
            'name' => 'employment_id',
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
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide an employment ID.',
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);

        // password_token
        $this->add([
            'name' => 'password_token',
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
                        'message' => 'Please provide password.'
                    ]
                ]
            ]
        ]);

        // agent_email
        $this->add([
            'name' => 'agent_email',
            'required' => false,
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
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => "The agent CC email address entered is not valid. Please enter a valid email address.",
                        'break_chain_on_failure' => true
                    ]
                ],
            ]
        ]);

        // agent_code
        $this->add([
            'name' => 'agent_code',
            'required' => false,
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
                        'message' => "Please provide an agent/agency code."
                    ]
                ]
            ]
        ]);

        // agent_password
        $this->add([
            'name' => 'agent_password',
            'required' => false,
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
                        'message' => "Please provide an agent/agency password."
                    ]
                ]
            ]
        ]);

        // start_date
        $this->add([
            'name' => 'start_date',
            'required' => false,
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
                        'message' => "Please select a valid start date."
                    ]
                ]
            ]
        ]);

        // course_selector
        $this->add([
            'name' => 'course_selector',
            'required' => false,
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
                        'message' => "Please select a course."
                    ]
                ]
            ]
        ]);
    }
}