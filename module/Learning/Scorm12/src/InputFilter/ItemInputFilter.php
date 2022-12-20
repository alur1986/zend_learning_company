<?php

namespace Scorm12\InputFilter;

use Zend\InputFilter\InputFilter as AbstractInputFilter;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;

class ItemInputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // activity_id
        $this->add([
            'name' => 'activity_id',
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
                        'message' => 'Please provide activity ID.'
                    ]
                ]
            ]
        ]);

        // item_id
        $this->add([
            'name' => 'item_id',
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
                        'message' => 'Please provide item ID.'
                    ]
                ]
            ]
        ]);

        // title
        $this->add([
            'name' => 'title',
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
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => 'Please provide a title.'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_LONG => 'Please provide a title between %min% and %max% characters in length.',
                            StringLength::TOO_SHORT => 'Please provide a title between %min% and %max% characters in length.'
                        ]
                    ]
                ]
            ]
        ]);

        // identifier
        $this->add([
            'name' => 'identifier',
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
                        'message' => 'Please provide item identifier.'
                    ]
                ]
            ]
        ]);

        // item_location
        $this->add([
            'name' => 'item_location',
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
                        'message' => 'Please provide the item launch file item location.'
                    ]
                ]
            ]
        ]);

        // is_visible
        $this->add([
            'name' => 'is_visible',
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
                        'message' => 'Please select the item visibility status.'
                    ]
                ]
            ]
        ]);

        // max_time_allowed
        $this->add([
            'name' => 'max_time_allowed',
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
                        'message' => 'Please provide the maximum time allowed for the item.'
                    ]
                ]
            ]
        ]);

        // prerequisites
        $this->add([
            'name' => 'prerequisites',
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
                        'message' => 'Please provide the item prerequisites.'
                    ]
                ]
            ]
        ]);

        // time_limit_action
        $this->add([
            'name' => 'time_limit_action',
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
                        'message' => 'Please select the time limit action for the item.'
                    ]
                ]
            ]
        ]);

        // mastery_score
        $this->add([
            'name' => 'mastery_score',
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
                        'message' => 'Please provide the item mastery score.'
                    ]
                ]
            ]
        ]);
    }
}