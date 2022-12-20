<?php

namespace Learner\InputFilter;

use Zend\Validator\File\NotExists;
use Zend\InputFilter\InputFilter;

class PhotoInputFilter extends InputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // user_id
        $this->add([
            'name' => 'user_id',
            'required' => false,
            'filters' => [],
            'validators' => []
        ]);

        // profile_photo
        $this->add([
            'name' => 'profile_photo',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => [
                        'extension' => 'jpg,jpeg,gif,png,bmp',
                        'message' => 'Files that have the extension "%extension%" are only allowed',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Zend\Validator\File\Size',
                    'options' => [
                        'max' => '1Mb',
                        'message' => 'File must not be more than %max% in size',
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);
    }
}