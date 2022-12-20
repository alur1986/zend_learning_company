<?php

namespace Scorm12\InputFilter;

use Zend\Validator\Hostname;
use Zend\Validator\File\UploadFile;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class FileUploadInputFilter extends AbstractInputFilter
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
                        'message' => 'Please provide activity ID.'
                    ]
                ]
            ]
        ]);

        // file_upload
        $this->add([
            'name' => 'file_upload',
            'required' => false,
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'There is no file selected',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                	'name' => 'Zend\Validator\File\UploadFile',
                	'options' => [
                		'message' => 'Please select a valid file to upload'
                	]
                ],
                [
                    'name' => 'Zend\Validator\File\Size',
                    'options' => [
                        'min' => '10B',
                        'max' => '300Mb',
                        'message' => 'File must not be less than %min% or more than %max% in size',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => [
                        'extension' => 'zip',
                        'message' => 'Files that have the extension "%extension%" are only allowed',
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);
    }
}
