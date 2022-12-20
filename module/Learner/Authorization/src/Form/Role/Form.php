<?php

namespace Authorization\Form\Role;

use Savve\Form\AbstractForm;

class Form extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string|null $name Name of the form (optional)
     * @param array $options Options for the form (optional)
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // id
        $this->add([
            'name' => 'id',
            'type' => 'Hidden'
        ]);

        // name
        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'options' => [
                'label' => 'Short name'
            ],
            'attributes' => [
                'placeholder' => 'Enter the short name of the role'
            ]
        ]);

        // title
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Role title'
            ],
            'attributes' => [
                'placeholder' => 'Enter role title'
            ]
        ]);

        // description
        $this->add([
            'name' => 'description',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Description'
            ],
            'attributes' => [
                'placeholder' => 'Enter description of the role'
            ]
        ]);

        // level_id
        $this->add([
            'name' => 'level_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Access Level'
            ],
            'attributes' => [
                'placeholder' => 'Select access level'
            ]
        ]);
    }
}