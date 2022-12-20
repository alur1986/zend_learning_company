<?php

namespace Report\XAPIInteractions\InputFilter;

use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class InputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // activityId
        $this->add([
            'name' => 'activityId',
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
                        'message' => 'Please select at least ONE activity.'
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
        ]);

        // show_from
        $this->add([
            'name' => 'show_from',
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
                    'name' => 'Callback',
                    'options' => [
                        'callback' => function  ($value, $context = [])
                        {
                            $currentDate = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                            $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                            return $startDate < $currentDate;
                        },
                        'message' => 'Start date must be no older than today\'s date'
                    ]
                ]
            ]
        ]);

        // show_to
        $this->add([
            'name' => 'show_to',
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
                    'name' => 'Callback',
                    'options' => [
                        'callback' => function  ($value, $context = [])
                        {
                            $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $context['show_from']);
                            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                            return $endDate >= $startDate;
                        },
                        'message' => 'Expiry date must be at least 1 day greater than the start date'
                    ]
                ]
            ]
        ]);

        // all_dates
        $this->add([
            'name' => 'all_dates',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Boolean'
                ],
                [
                    'name' => 'Null'
                ]
            ]
        ]);

        // learner_status
        $this->add([
            'name' => 'learner_status',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Null'
                ]
            ],
        ]);

        $this->add([
            'name' => 'show_assessment_only',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Boolean'
                ],
                [
                    'name' => 'Null'
                ]
            ]
        ]);

        $this->add([
            'name' => 'action_verb',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => "Please select at least ONE action verb."
                    ]
                ]
            ]
        ]);

    }
}