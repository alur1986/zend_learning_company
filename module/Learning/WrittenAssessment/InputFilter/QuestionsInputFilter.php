<?php

namespace WrittenAssessment\InputFilter;

use Zend\I18n\Validator\Alnum;
use Zend\Validator\Callback;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;

class QuestionsInputFilter extends InputFilter
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
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Please provide activity ID.'
                        ]
                    ]
                ]
            ]
        ]);

        // assessment_id
        $this->add([
            'name' => 'assessment_id',
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

        // proof_of_evidence
        $this->add([
            'name' => 'proof_of_evidence',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToUpper'
                ]
            ]
        ]);

        // assessor_comments
        $this->add([
            'name' => 'assessor_comments',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToUpper'
                ]
            ]
        ]);

        // show_score
        $this->add([
            'name' => 'show_score',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToUpper'
                ]
            ]
        ]);

        // pass_score
        $this->add([
            'name' => 'pass_score',
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
                    'name' => 'Regex',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => '/^[0-9][\.\d]*(,\d+)?$/',
                        'messages' => [
                            Regex::NOT_MATCH => "Please provide pass score in numeric form."
                        ]
                    ]
                ],
                [
                    'name' => 'Between',
                    'options' => [
                        'min' => 0,
                        'max' => 100,
                        'messages' => [
                            Between::NOT_BETWEEN => "Please provide pass score in numeric form between %min% and %max%."
                        ]
                    ]
                ]
            ]
        ]);

        // show_status
        $this->add([
            'name' => 'show_status',
            'required' => false,
            'allowEmpty' => true,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToUpper'
                ]
            ],
        ]);

        // learner_comments
        $this->add([
            'name' => 'learner_comments',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToUpper'
                ]
            ]
        ]);

        // introduction
        $this->add([
            'name' => 'introduction',
            'required' => true,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Please provide the introduction text.'
                        ]
                    ]
                ]
            ]
        ]);
    }
}