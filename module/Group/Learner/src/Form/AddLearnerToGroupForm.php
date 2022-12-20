<?php

namespace Group\Learner\Form;

use Savve\Form\AbstractForm;

class AddLearnerToGroupForm extends AbstractForm
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
            'type' => 'Select',
            'label' => 'Add learner to group',
            'options' => [
                'empty_option' => "Select group"
            ]
        ]);

        // learner_id: hidden
        $this->add([
            'name' => 'learner_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
        ]);
    }
}