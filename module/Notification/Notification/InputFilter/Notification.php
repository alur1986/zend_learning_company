<?php

namespace Notification\InputFilter;

use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class Notification extends InputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // notification id
        $this->add([
            'name' => 'notification_id',
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
                        'message' => 'Please provide a notification ID.'
                    ]
                ]
            ]
        ]);

        // subject
        $this->add([
            'name' => 'subject',
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
                        'message' => 'Please provide a subject.',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'max' => 140,
                        'min' => 5,
                        'message' => "Subject must be between %min% and %max% characters long."
                    ]
                ]
            ]
        ]);

        // message
        $this->add([
            'name' => 'message',
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
                        'message' => 'Please provide a message.',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 5,
                        'message' => "Message must be at least %min% characters long."
                    ]
                ]
            ]
        ]);

        // sender_name
        $this->add([
            'name' => 'sender_name',
            'required' => false,
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
                        'message' => 'Sender name is required'
                    ]
                ]
            ]
        ]);

        // sender_email
        $this->add([
            'name' => 'sender_email',
            'required' => false,
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
                        'message' => 'Sender email is required',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => "Email address should be in the format email -handle@domain-name.com",
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);

        // active_from
        $this->add([
            'name' => 'active_from',
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

        // active_to
        $this->add([
            'name' => 'active_to',
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

        // learner_id
        $this->add([
            'name' => 'learner_id',
            'required' => false,
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
                        'message' => 'Please select at least ONE learner.'
                    ]
                ]
            ]
        ]);

        // group_id
        $this->add([
            'name' => 'group_id',
            'required' => false,
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
                        'message' => 'Please select at least ONE group.'
                    ]
                ]
            ]
        ]);

        // send_to_all
        $this->add([
            'name' => 'send_to_all',
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