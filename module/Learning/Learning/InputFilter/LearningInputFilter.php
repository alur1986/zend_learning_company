<?php

namespace Learning\InputFilter;

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

class LearningInputFilter extends InputFilter
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
                        'message'=> 'Please provide activity ID.'
                    ]
                ]
            ]
        ]);

        // activity_type
        $this->add([
            'name' => 'activity_type',
            'required' => true,
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
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide activity type.'
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
                        'message' => 'Please provide site ID.'
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
                        'message'=> 'Please provide a title.'
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

        // code
        $this->add([
            'name' => 'code',
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
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => 'Please provide a code.'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 3,
                        'max' => 32,
                        'messages' => [
                            StringLength::TOO_LONG => 'Please provide a code  between %min% and %max% characters in length.',
                            StringLength::TOO_SHORT => 'Please provide a code  between %min% and %max% characters in length.'
                        ]
                    ]
                ],
                [
                    'name' => 'Alnum',
                    'options' => [
                        'allowWhiteSpace' => true,
                        'messages' => [
                            Alnum::NOT_ALNUM => 'Code can only contain letters and numbers.'
                        ]
                    ]
                ]
            ]
        ]);

        // version
        $this->add([
            'name' => 'version',
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
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => 'Please provide a version number.'
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 1,
                        'max' => 15,
                        'messages' => [
                            StringLength::TOO_LONG => 'Please provide a version number between %min% and %max% characters in length.',
                            StringLength::TOO_SHORT => 'Please provide a version number between %min% and %max% characters in length.'
                        ]
                    ]
                ],
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

        // keywords
        $this->add([
            'name' => 'keywords',
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
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 1024,
                        'messages' => [
                            StringLength::TOO_LONG => 'Keywords can be a maximum of %max% characters in length.',
                            StringLength::TOO_SHORT => 'Keywords can be a minimum of %min% characters in length.'
                        ]
                    ]
                ]
            ]
        ]);

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
                ],
                [
                    'name' => 'StringToLower'
                ],
                [
                    'name' => 'Null'
                ]
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_LONG => 'Category can be a maximum of %max% characters in length.',
                            StringLength::TOO_SHORT => 'Category can be a minimum of %min% characters in length.'
                        ]
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

        // catalog_description
        $this->add([
            'name' => 'catalog_description',
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
                            'strong'],
                        'allowAttribs' => ['href','style','title','target']
                    ]
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide a catalog description.'
                    ]
                ]
            ]
        ]);

        // duration
        $this->add([
            'name' => 'duration',
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
                    'name' => 'Null'
                ],
                [
                	'name' => 'Digits'
                ]
            ],
            'validators' => [
                [
                	'name' => 'Zend\I18n\Validator\IsFloat',
                	'options' => [
                		'message' => 'Duration entered must be numeric'
                	]
                ]
            ]
        ]);

        // direct_cost
        $this->add([
            'name' => 'direct_cost',
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
                            Regex::NOT_MATCH => "Please provide direct cost in numeric form."
                        ]
                    ]
                ]
            ]
        ]);

        // indirect_cost
        $this->add([
            'name' => 'indirect_cost',
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
                            Regex::NOT_MATCH => "Please provide indirect cost in numeric form."
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

        // auto_approve
        $this->add([
            'name' => 'auto_approve',
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

        // needs_enrolment
        $this->add([
            'name' => 'needs_enrolment',
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

        // description
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
                        'allowTags' => ['ol', 'ul',
                        'li',
                        'p',
                        'span',
                        'em',
                        'a',
                        'strong'],
                        'allowAttribs' => ['href','style']
                    ]
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide a learning activity description.'
                    ]
                ]
            ]
        ]);

        // learning objective
        $this->add([
            'name' => 'learning_objective',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Null'
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
                            'strong'],
                        'allowAttribs' => ['href','style']
                    ]
                ]
            ]
        ]);

        // prerequisites objective
        $this->add([
            'name' => 'prerequisites',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StringTrim'
                ],
                [
                    'name' => 'Null'
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
                            'strong'],
                        'allowAttribs' => ['href','style']
                    ]
                ]
            ]
        ]);

        // prerequisites objective
        $this->add([
            'name' => 'prerequisite',
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

        // cpd
        $this->add([
            'name' => 'cpd',
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
                            Regex::NOT_MATCH => "Please provide CPD points in numeric form."
                        ]
                    ]
                ],
                [
                    'name' => 'Between',
                    'options' => [
                        'min' => 0,
                        'max' => 100,
                        'messages' => [
                            Between::NOT_BETWEEN => "Please provide CPD points in numeric form between %min% and %max%."
                        ]
                    ]
                ]
            ]
        ]);

        // auto_distribute
        $this->add([
            'name' => 'auto_distribute',
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
                    'name' => 'NotEmpty',
                    'options' => [
                        'message'=> 'You must select either Distribute on Registration or Distribute on Login.',
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);

        // auto_distribute_on_registration
        $this->add([
            'name' => 'auto_distribute_on_registration',
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

        // auto_distribute_on_login
        $this->add([
            'name' => 'auto_distribute_on_login',
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

        // ordering
        $this->add([
            'name' => 'ordering',
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

        // auto_distribute_delay
        $this->add([
            'name' => 'auto_distribute_delay',
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

        // plan_id
        $this->add([
            'name' => 'plan_id',
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

        // licensed
        $this->add([
            'name' => 'licensed',
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
                            Regex::NOT_MATCH => "Please provide the maximum number of licenses in numeric form."
                        ]
                    ]
                ],
                [
                    'name' => 'Between',
                    'options' => [
                        'min' => 1,
                        'max' => 9999,
                        'messages' => [
                            Between::NOT_BETWEEN => "Please provide the maximum number of licenses in numeric form between %min% and %max%."
                        ]
                    ]
                ]
            ]
        ]);
    }
}