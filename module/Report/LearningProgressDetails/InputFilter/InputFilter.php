<?php

namespace Report\LearningProgressDetails\InputFilter;

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
        // activity_id
        $this->add([
            'name' => 'activity_id',
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
                            $startDate = new \DateTime(date('Y-m-d', strtotime($context['show_from'])));
                            $endDate = new \DateTime(date('Y-m-d', strtotime($value)));
                            return ($startDate < $endDate) ? true : false;
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

        // tracking_status
        $this->add([
            'name' => 'tracking_status',
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
                        'message' => "Please select at least ONE learning progress status."
                    ]
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
    }
}