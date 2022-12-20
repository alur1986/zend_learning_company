<?php

namespace Authorization\Form\Rule;

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

        // site_id
        $this->add([
            'name' => 'site_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Which site this rule belongs to?',
                'empty_option' => [
                    'label' => "All sites (global)",
                    'value' => null
                ],
            ]
        ]);

        // role_id
        $this->add([
            'name' => 'role_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Which role will this rule applies for?',
                'value_options' => []
            ]
        ]);

        // resource_id
        $this->add([
            'name' => 'resource_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Which resource will this rule will be applied to?',
                'value_options' => []
            ]
        ]);

        // permission
        $this->add([
            'name' => 'permission',
            'type' => 'Radio',
            'options' => [
                'label' => 'What kind of permission is applied to this rule?',
                'value_options' => [
                    ['label' => 'Allow', 'value' => 'allow', 'selected' => 'selected'],
                    ['label' => 'Deny', 'value' => 'deny'],
                ],
            ]
        ]);
    }
}