<?php

namespace Authorization\Form\Resource;

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

        // resource
        $this->add([
            'name' => 'resource',
            'type' => 'Text',
            'options' => [
                'label' => 'Resource'
            ],
            'attributes' => [
                'placeholder' => 'Enter the resource'
            ]
        ]);

        // title
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Resource title'
            ],
            'attributes' => [
                'placeholder' => 'Enter resource title'
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
                'placeholder' => 'Enter description of the resource'
            ]
        ]);

        // type
        $this->add([
            'name' => 'type',
            'type' => 'Select',
            'options' => [
                'label' => 'Type of resource',
                'value_options' => [
                    [
                        'label' => 'Route',
                        'value' => 'route'
                    ],
                    [
                        'label' => 'Block',
                        'value' => 'block'
                    ],
                    [
                        'label' => 'Controller::Action',
                        'value' => 'controller'
                    ],
                    [
                        'label' => 'URI / URL',
                        'value' => 'uri'
                    ]
                ]
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