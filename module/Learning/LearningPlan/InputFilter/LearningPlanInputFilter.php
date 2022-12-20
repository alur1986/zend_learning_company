<?php

namespace LearningPlan\InputFilter;

use Zend\I18n\Filter\NumberFormat;
use Zend\I18n\Validator\IsFloat;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\Digits;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;

class LearningPlanInputFilter extends InputFilter
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // plan_id
        $this->add([
            'name' => 'plan_id',
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
                        'message' => 'Please provide Plan ID.'
                    ]
                ]
            ]
        ]);

        // site_id
        $this->add([
            'name' => 'site_id',
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
                        'message' => 'Please provide Site ID.'
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

        // catalog_description
        $this->add([
            'name' => 'description',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'StripTags',
                    'options' => [
                        'allowTags' => ['ol',
                            'ul',
                            'li',
                            'p',
                            'span',
                            'em',
                            'a',
                            'strong',
                            'img'],
                        'allowAttribs' => ['href', 'style', 'title', 'target', 'src', 'alt', 'style', 'width', 'height']
                    ]
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide a Learning Plan description.'
                    ]
                ]
            ]
        ]);

        // catalogue_thumb
        $this->add([
            'name' => 'catalog_thumb',
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
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 0,
                        'max' => 1024,
                        'messages' => [
                            StringLength::TOO_LONG => 'Catalog thumb can be a maximum of %max% characters in length.',
                            StringLength::TOO_SHORT => 'Catalog thumb can be a minimum of %min% characters in length.'
                        ]
                    ]
                ]
            ]
        ]);

        // catalogue_image
        $this->add([
            'name' => 'catalog_image',
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
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 0,
                        'max' => 1024,
                        'messages' => [
                            StringLength::TOO_LONG => 'Catalog image can be a maximum of %max% characters in length.',
                            StringLength::TOO_SHORT => 'Catalog image can be a minimum of %min% characters in length.'
                        ]
                    ]
                ]
            ]
        ]);

        // catalogue_display
        $this->add([
            'name' => 'catalog_display',
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

        // status
        $this->add([
            'name' => 'status',
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

        // config
        $this->add([
            'name' => 'confirm_ordering',
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