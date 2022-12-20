<?php

namespace OnTheJobAssessment\Form\Fieldset;

use Zend\Form\Fieldset;

class CreateQuestionFieldset extends Fieldset
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

        // question_id
        $this->add([
            'name' => 'question_id',
            'type' => 'Hidden'
        ]);

        // status
        $this->add([
            'name' => 'status',
            'type' => 'Hidden'
        ]);

        // question
        $this->add([
            'name' => 'question',
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter your question text here',
        		'required' => true
            ],
            'options' => [
                'label' => 'Factor'
            ]
        ]);

        // sort_order
        $range = range(1, 50);
        $values = array_combine($range, $range);
        $this->add([
            'name' => 'sort_order',
            'type' => 'Select',
            'attributes' => [
                'placeholder' => 'Question priority'
            ],
            'options' => [
                'label' => 'Sorting order',
                'value_options' => $values
            ]
        ]);
    }
}