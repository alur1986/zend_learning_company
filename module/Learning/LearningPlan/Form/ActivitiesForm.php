<?php

namespace LearningPlan\Form;

use Savve\Form\AbstractForm;

class ActivitiesForm extends AbstractForm
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

        // plan_id
        $this->add([
            'name' => 'plan_id',
            'type' => 'Hidden'
        ]);

        // site_id
        $this->add([
            'name' => 'site_id',
            'type' => 'Hidden'
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
                'label' => 'Available Activities'
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
                'label' => 'Selected / Current Activities'
            ],
            'attributes' => [
                'multiple' => true
            ]
        ]);

        // confirm_reordering : radio
        $this->add([
            'name' => 'confirm_ordering',
            'type' => 'Radio',
            'options' => [
                'label' => 'Save Selected Ordering?',
                'value_options' => [
                    ['label' => 'No', 'value' => '0', 'selected' => 'selected'],
                    ['label' => 'Yes', 'value' => '1'],
                ],
                'description' => 'Confirms that you want the distribution ordering updated to match the ordering within the list'
            ],
            'attributes' => [
                'placeholder' => 'Confirms that you want the distribution ordering updated to match the ordering within the list'
            ]
        ]);
    }
}