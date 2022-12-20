<?php

namespace Learner\InputFilter;

use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class DistributionFilter extends AbstractInputFilter
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
                        'message' => 'Please select at least ONE activity.'
                    ]
                ]
            ]
        ]);

        // learner_id
        $this->add([
            'name' => 'user_id',
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
                        'message' => 'Please select at least ONE learner.'
                    ]
                ]
            ]
        ]);

        // distribution_date
        $this->add([
            'name' => 'distribution_date',
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
        ]);

        // expiry_date
        $this->add([
            'name' => 'expiry_date',
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
                            $startDate = new \DateTime(date('Y-m-d', strtotime($context['distribution_date'])));
                            $endDate = new \DateTime(date('Y-m-d', strtotime($value)));
                            return ($startDate < $endDate) ? true : false;
                        },
                        'message' => 'Expiry date must be at least 1 day greater than the start date'
                    ]
                ]
            ]
        ]);
    }
}