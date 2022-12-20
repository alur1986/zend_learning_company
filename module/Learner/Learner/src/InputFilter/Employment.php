<?php

namespace Learner\InputFilter;

use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\Validator\EmailAddress;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class Employment extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // user_id
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
            ]
        ]);

        // employment_id
        $this->add([
            'name' => 'employment_id',
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

        // position
        $this->add([
            'name' => 'position',
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

        // location
        $this->add([
            'name' => 'location',
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

        // employment_type
        $this->add([
            'name' => 'employment_type',
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

        // cost_centre
        $this->add([
            'name' => 'cost_centre',
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

        // manager
        $this->add([
            'name' => 'manager',
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

        // start_date
        $this->add([
            'name' => 'start_date',
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

        // end_date
        $this->add([
            'name' => 'end_date',
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
    }
}