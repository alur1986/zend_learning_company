<?php

namespace Report\MyLearning\Form;

use Savve\Form\AbstractForm;

class TemplateForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // template_id : hidden
        $this->add([
            'name' => 'template_id',
            'type' => 'Hidden',
            'required' => true
        ]);

        // title : text
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Title'
            ],
            'attributes' => [
                'placeholder' => 'Enter template title'
            ]
        ]);

        // description : textarea
        $this->add([
            'name' => 'description',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Description'
            ],
            'attributes' => [
                'placeholder' => 'Enter template description'
            ]
        ]);

        // config : textarea
        $this->add([
            'name' => 'config',
            'type' => 'Hidden',
            'options' => [
                'label' => 'Config'
            ]
        ]);

        // available_columns : multiselect
        $this->add([
            'name' => 'available_columns',
            'type' => 'Select',
            'options' => [
                'label' => 'Available columns'
            ],
            'attributes' => [
                'multiple' => true
            ]
        ]);

        // selected_columns : multiselect
        $this->add([
            'name' => 'selected_columns',
            'type' => 'Select',
            'options' => [
                'label' => 'Selected columns'
            ],
            'attributes' => [
                'multiple' => true
            ]
        ]);
    }
}