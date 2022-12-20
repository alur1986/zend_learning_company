<?php

namespace Report\LearningPlaylist\Form;

use Savve\Form\AbstractForm;

class ActivitiesForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, array $options = [])
    {
        parent::__construct($name, $options);

        // plan_id : radio
        $this->add([
            'name' => 'plan_id',
            'type' => 'Radio',
            'options' => [
                'label' => 'Select a learning playlist',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);
    }
}