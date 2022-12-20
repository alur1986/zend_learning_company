<?php

namespace Report\LearningProgressSummary\InputFilter;

use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class TemplateInputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // template_id
        $this->add([
            'name' => 'template_id',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => "Please enter the template ID."
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
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StripTags'
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => "Please enter a title."
                    ]
                ]
            ]
        ]);

        // description
        $this->add([
            'name' => 'description',
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

        // available_columns
        $this->add([
            'name' => 'available_columns',
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

        // selected_columns
        $this->add([
            'name' => 'selected_columns',
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

        // config
        $this->add([
            'name' => 'config',
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
                        'message' => "Columns were not selected"
                    ]
                ]
            ]
        ]);
    }
}