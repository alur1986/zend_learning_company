<?php

namespace Learner\Form;

use Savve\Form\AbstractForm;

class DistributionForm extends AbstractForm
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

        // activity_id
        $this->add([
            'name' => 'activity_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Activity:',
                'empty_option' => 'Select a Scorm/Resource Activity',
                'value_options' => []
            ]
        ]);

        // distribution_date : datetime
        $distributionDate = new \DateTime();
        $this->add([
            'name' => 'distribution_date',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Distribute on:',
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)',
                'value' => $distributionDate
            ]
        ]);

        // expiry_date : datetime
        $this->add([
            'name' => 'expiry_date',
            'type' => 'DateTime',
            'options' => [
                'label' => 'Expires on:',
                'format' => 'Y-m-d H:i:s'
            ],
            'attributes' => [
                'placeholder' => 'Enter date and time (yyyy-mm-dd hh:ii)'
            ]
        ]);
    }
}
