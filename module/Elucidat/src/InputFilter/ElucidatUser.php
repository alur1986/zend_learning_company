<?php

namespace Elucidat\InputFilter;

use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class ElucidatUser extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // id
        $this->add([
            'name' => 'id',
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
            ]
        ]);


        // site_id
        $this->add([
                       'name' => 'account_id',
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
                       ]
                   ]);


        // site_id
        $this->add([
                       'name' => 'user_id',
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
                       ]
                   ]);

        // site_id
        $this->add([
                       'name' => 'has_elucidat_access',
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



        // email
        $this->add([
                       'name' => 'elucidat_email',
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
                                   'message' => 'Please provide a valid email address.',
                                   'break_chain_on_failure' => true
                               ]
                           ],
                           [
                               'name' => 'EmailAddress',
                               'options' => [
                                   'message' => "The email address entered is not valid. Please enter a valid email address.",
                                   'break_chain_on_failure' => true
                               ]
                           ],
                       ]
                   ]);
    }
}