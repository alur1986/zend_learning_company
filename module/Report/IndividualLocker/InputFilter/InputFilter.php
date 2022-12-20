<?php

namespace Report\IndividualLocker\InputFilter;

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
        // category_id
        $this->add([
            'name' => 'category_id',
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
                ],
                [
                    'name' => 'Null'
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
                            $currentDate = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                            $startDate = \DateTime::createFromFormat('Y-m-d', $value);
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
                            $startDate = \DateTime::createFromFormat('Y-m-d', strtotime($context['show_from']));
                            $endDate = \DateTime::createFromFormat('Y-m-d', strtotime($value));
                            return $endDate <= $startDate;
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

        // verification_status
        $this->add([
            'name' => 'verification_status',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
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
            ]
        ]);
    }
}