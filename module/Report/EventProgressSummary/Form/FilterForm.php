<?php

namespace Report\EventProgressSummary\Form;

use Savve\Form\AbstractForm;

class FilterForm extends AbstractForm
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

        // filter_id : hidden
        $this->add([
            'name' => 'filter_id',
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
                'placeholder' => 'Enter filter title'
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
                'placeholder' => 'Enter filter description'
            ]
        ]);
    }
}