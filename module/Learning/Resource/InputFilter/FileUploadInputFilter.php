<?php

namespace Resource\InputFilter;

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
//                 [
//                     'name' => 'Zend\Validator\File\Size',
//                     'options' => [
//                         'min' => null,
//                         'max' => '12Mb',
//                         'messages' => [
//                 			\Zend\Validator\File\Size::TOO_SMALL  => 'File must not be less than %min% bytes in size.',
//                 			\Zend\Validator\File\Size::TOO_BIG => 'File must not be more than %max% bytes in size.',
//                 			\Zend\Validator\File\Size::NOT_FOUND => 'Please upload a file.',
//                 		],
//                         'break_chain_on_failure' => true
//                     ]
//                 ],
//                 [
//                     'name' => 'Zend\Validator\File\Extension',
//                     'options' => [
//                         'extension' => 'doc,pdf,docx,rtf,html,htm,zip,rar,csv,xlsx,xls,ppt,pps,txt,jpg,jpeg,gif,png,bmp',
//                         'message' => 'Files that have the extension "%extension%" are only allowed',
//                         'break_chain_on_failure' => true
//                     ]
//                 ],
//                 [
//                 	'name' => 'Zend\Validator\File\UploadFile',
//                 	'options' => [
//                 		'message' => 'Please select a valid file to upload',
//                 		'break_chain_on_failure' => true
//                 	]
//                 ],
//                 [
//                 	'name' => 'Zend\Validator\Callback',
//                 	'options' => [
//                 		'callback' => function($value, $context = []){
//                 			$elementUrl = $context['url'];
//                 			if (empty($elementUrl) && ($value['size'] === 0 || $value['error'] === 4)) {
//                 			    return false;
//                 			}
//                 			return true;
//                 		},
//                 		'message' => "Please upload a file or enter the URL of the resource.",
//                 		'break_chain_on_failure' => true
//                 	]
//                 ]
            ]
        ]);

        // url
        $this->add([
            'name' => 'url',
            'required' => false,
            'allow_empty' => true,
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
                    'name' => 'UriNormalize',
                    'options' => [
                        'enforcedScheme' => 'http'
                    ]
                ]
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'message' => 'Please provide resource URL.',
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Uri',
                    'options' => [
                        'allowRelative' => false,
                        'message' => "Must be a valid URL format",
                        'break_chain_on_failure' => true
                    ]
                ],
                [
                    'name' => 'Zend\Validator\Callback',
                    'options' => [
                        'callback' => function  ($value, $context = [])
                        {
                            $elementFileUpload = $context['file_upload'];
                            if (($elementFileUpload['size'] === 0 || $elementFileUpload['error'] === 4) && (empty($value))) {
                                return false;
                            }
                            return true;
                        },
                        'message' => "Please upload a file or enter the URL of the resource."
                    ]
                ]
            ]
        ]);

        // title
        $this->add([
            'name' => 'title',
            'required' => true,
            'allow_empty' => false,
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
                        'message' => 'Please provide a valid title.',
                        'break_chain_on_failure' => true
                    ]
                ]
            ]
        ]);
    }
}