<?php

namespace Learner\Form;

// use Zend\Form\Form as AbstractForm;
use Savve\Form\AbstractForm;

class ImportForm extends AbstractForm
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

        // file_upload : file
        $this->add([
            'name' => 'file_upload',
            'type' => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'Upload File',
                'type' => 'File',
                'name' => 'file_upload'
            ]
        ]);

        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit',
                'value' => 'submit'
            ],
            'options' => [
                'label' => 'Upload'
            ]
        ]);
    }
}