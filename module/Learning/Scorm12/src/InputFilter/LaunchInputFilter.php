<?php

namespace Scorm12\InputFilter;

use Zend\Validator\GreaterThan;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class LaunchInputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {

        // allowed_attempts
        $this->add([
            'name' => 'allowed_attempts',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ]
        ]);

        // allow_review_on_completion
        $this->add([
            'name' => 'allow_review_on_completion',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ]
        ]);

        $this->add([
            'name' => 'allow_tracking_override_after_completion',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ]
        ]);

        // allow_review_on_fail
        $this->add([
            'name' => 'allow_review_on_fail',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ]
        ]);

        // window_scrollable
        $this->add([
            'name' => 'window_scrollable',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ]
        ]);

        // window_width
        $this->add([
            'name' => 'window_width',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ],
            'validators' => [
                [
                    'name' => 'GreaterThan',
                    'options' => [
                        'min' => 100,
                        'inclusive' => true,
                        'messages' => [
                            GreaterThan::NOT_GREATER => "Please provide the course width that is at least %min%."
                        ]
                    ]
                ]
            ]
        ]);

        // window_height
        $this->add([
            'name' => 'window_height',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags'
                ],
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StringToLower'
                ]
            ],
            'validators' => [
                [
                    'name' => 'GreaterThan',
                    'options' => [
                        'min' => 100,
                        'inclusive' => true,
                        'messages' => [
                            GreaterThan::NOT_GREATER => "Please provide the course width that is at least %min%."
                        ]
                    ]
                ]
            ]
        ]);
    }
}