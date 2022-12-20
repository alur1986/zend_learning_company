<?php

namespace Resource\Form;

use Savve\Form\AbstractForm;

class UploadFileForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, $options = [])
    {
        parent::__construct($name, $options);

        // activity_id
        $this->add([
            'name' => 'activity_id',
            'type' => 'Hidden'
        ]);

        // file_upload
        $this->add([
            'name' => 'file_upload',
            'type' => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'Upload File'
            ],
            'attributes' => [
                'placeholder' => 'Select file to upload'
            ]
        ]);

        // url
        $this->add([
            'name' => 'url',
            'type' => 'Text',
            'attributes' => [
                'placeholder' => 'Enter url of the external resource link'
            ],
            'options' => [
                'label' => 'External Resource link'
            ]
        ]);

        // title
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'attributes' => [
                'placeholder' => 'Enter title of the resource'
            ],
            'options' => [
                'label' => 'Title'
            ]
        ]);

        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Submit'
            ],
            'options' => [
                'label' => 'Upload'
            ]
        ]);
    }
}