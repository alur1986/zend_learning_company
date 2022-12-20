<?php

namespace OnTheJobAssessment\InputFilter;

use Zend\InputFilter\CollectionInputFilter as ZendCollectionInputFilter;
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
use Zend\InputFilter\InputFilter as ZendInputFilter;

class QuestionInputFilter extends ZendInputFilter
{

    /**
     * Construct
     */
    public function __construct ()
    {
        // question_id
        $this->add([
            'name' => 'question_id',
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
                            NotEmpty::IS_EMPTY => 'Please provide the question ID.'
                        ]
                    ]
                ]
            ]
        ]);

        // question
        $this->add([
            'name' => 'question',
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
                            NotEmpty::IS_EMPTY => 'Please provide a question'
                        ]
                    ]
                ]
            ]
        ]);

        // sort_order
        $this->add([
            'name' => 'sort_order',
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

        // status
        $this->add([
            'name' => 'status',
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
    }
}