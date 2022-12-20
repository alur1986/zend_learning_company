<?php

namespace Learner\InputFilter;

use Zend\Validator\File\Size;
use Zend\Validator\Callback;
use Zend\Validator\Hostname;
use Zend\Validator\File\UploadFile;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\InputFilter as AbstractInputFilter;

class Import extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        // file_upload
        $this->add([
            'name' => 'file_upload',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'There is no file selected',
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
                    'name' => 'Zend\Validator\File\Size',
                    'options' => [
                        'min' => '10B',
                        'max' => '12Mb',
                        'message' => 'Cannot import the CSV file data. No data was found within the CSV file, or, the data was incomplete or invalid.',
                        'break_chain_on_failure' => true
                    ]
                ],
            ]
        ]);
    }
}