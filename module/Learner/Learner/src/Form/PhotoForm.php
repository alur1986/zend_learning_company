<?php

namespace Learner\Form;

use Savve\Form\AbstractForm;

class PhotoForm extends AbstractForm
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

        // user_id : hidden
        $this->add([
            'name' => 'user_id',
            'type' => 'Hidden'
        ]);

        // profile_photo
        $this->add([
            'name' => 'profile_photo',
            'type' => 'Zend\Form\Element\File',
            'options' => [
                'label' => 'Upload profile photo - 1MB Maximum Size'
            ],
            'attributes' => [
                'placeholder' => 'Select photo to upload'
            ]
        ]);
    }
}