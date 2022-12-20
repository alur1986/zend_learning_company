<?php

namespace Group\Learner\InputFilter;

use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class InputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // group_id
        $this->add([
            'name' => 'group_id',
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
                        'message' => 'Group ID was not provided.'
                    ]
                ]
            ]
        ]);

        // learner_id
        $this->add([
            'name' => 'learner_id',
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
                        'message' => 'Please select at least ONE learner.'
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
                    'name' => 'Zend\Validator\File\Size',
                    'options' => [
                        'min' => '10B',
                        'max' => '12Mb',
                        'message' => 'Cannot import the CSV file data. No data was found within the CSV file, or, the data was incomplete or invalid.',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => [
                        'extension' => 'csv',
                        'message' => 'Files that have the extension "%extension%" are only allowed',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Zend\Validator\File\UploadFile',
                    'options' => [
                        'message' => 'Please select a valid file to upload'
                    ]
                ]
            ]
        ]);
    }
}