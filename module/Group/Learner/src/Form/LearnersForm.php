<?php

namespace Group\Learner\Form;

use Savve\Form\AbstractForm;

class LearnersForm extends AbstractForm
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

        // learner_id
        $this->add([
            'name' => 'learner_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Learners',
                'use_hidden_element' => true,
                'value_options' => []
            ]
        ]);
    }
}