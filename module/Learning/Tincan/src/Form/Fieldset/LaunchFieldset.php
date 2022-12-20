<?php

namespace Tincan\Form\Fieldset;

use Zend\Form\Fieldset as AbstractFieldset;

class LaunchFieldset extends AbstractFieldset
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

        // allowed_attempts
        $range = range(1, 10);
        $values = array_combine($range, $range);
        $this->add([
            'name' => 'allowed_attempts',
            'type' => 'Select',
            'options' => [
                'label' => 'Allowed attempts',
                'value_options' => $values
            ],
            'attributes' => [
                'placeholder' => 'Allowed Attempts'
            ]
        ]);

        // allow_browse
        $this->add([
            'name' => 'allow_browse',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow Browse',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // allow_review_on_completion
        $this->add([
            'name' => 'allow_review_on_completion',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow "Review" when passed/completed',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // for captiviate course we need to keep overriding status
        $this->add([
                   'name' => 'allow_tracking_override_after_completion',
                   'type' => 'Checkbox',
                   'options' => [
                       'label' => 'Allow override of tracking information after the course is completed',
                       'use_hidden_element' => true,
                       'checked_value' => '1',
                       'unchecked_value' => '0'
                   ]
                   ]);


        // allow_review_on_fail
        $this->add([
            'name' => 'allow_review_on_fail',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Allow "Review" when failed',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // window_scrollable
        $this->add([
            'name' => 'window_scrollable',
            'type' => 'Checkbox',
            'options' => [
                'label' => 'Scrollable course window',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // window_width
        $this->add([
            'name' => 'window_width',
            'type' => 'Text',
            'options' => [
                'label' => 'Width of the course window'
            ],
            'attributes' => [
                'placeholder' => 'Width of the course window',
                'value' => 1012
            ]
        ]);

        // window_height
        $this->add([
            'name' => 'window_height',
            'type' => 'Text',
            'options' => [
                'label' => 'Height of the course window'
            ],
            'attributes' => [
                'placeholder' => 'Height of the course window',
                'value' => 688
            ]
        ]);

    }
}