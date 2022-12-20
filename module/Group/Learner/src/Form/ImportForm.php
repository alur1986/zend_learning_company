<?php

namespace Group\Learner\Form;

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

        // group_id: hidden
        $this->add([
            'name' => 'group_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
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
    }
}