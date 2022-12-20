<?php

namespace Tincan\Form;

use Savve\Form\AbstractForm;

class ItemForm extends AbstractForm
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

        // activity_id
        $this->add([
            'name' => 'activity_id',
            'type' => 'Hidden'
        ]);

        // item_id
        $this->add([
            'name' => 'item_id',
            'type' => 'Hidden'
        ]);

        // title
        $this->add([
            'name' => 'title',
            'type' => 'Text',
            'options' => [
                'label' => 'Title'
            ],
            'attributes' => [
                'placeholder' => 'Enter title of the item'
            ]
        ]);

        // identifier
        $this->add([
            'name' => 'identifier',
            'type' => 'Text',
            'options' => [
                'label' => 'Identifier'
            ],
            'attributes' => [
                'placeholder' => 'Enter identifier of the item'
            ]
        ]);

        // item_location
        $this->add([
            'name' => 'item_location',
            'type' => 'Text',
            'options' => [
                'label' => 'Launch item location'
            ],
            'attributes' => [
                'placeholder' => 'Enter item location of the item'
            ]
        ]);

        // item_location
        $this->add([
            'name' => 'item_iri',
            'type' => 'Text',
            'options' => [
                'label' => 'Activity IRI (Base Activity ID. This is read from the xAPI manifest file upon upload)'
            ],
            'attributes' => [
                'readonly' => 'readonly',
                'placeholder' => 'This must contain the activities IRI code. If not present, launch URL (Launch Item Location) for the activity will be used'
            ]
        ]);

        // item_activities
        $this->add([
            'name' => 'item_activities',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Item Sub-Activities (Required for the default xAPI Experience reporting views)'
            ],
            'attributes' => [
                'placeholder' => 'Enter the Sub-Activities that can be used in the Learner based reporting. Enter a comma separated string, and ensure the spelling/case/syntax match those saved in the LRS statements. Do NOT include the base IRI and no leading forward-slashes. Example: Sub-Activity-one,Sub-Activity-two,Sub-Activity-three,Section2/Sub-Activity-four.'
            ]
        ]);


        // is_visible
        $this->add([
            'name' => 'is_visible',
            'required' => true,
            'type' => 'Select',
            'options' => [
                'label' => 'Item visibility',
                'value_options' => [
                    1 => 'Yes',
                    0 => 'No'
                ]
            ]
        ]);

        // max_time_allowed
        $this->add([
            'name' => 'max_time_allowed',
            'type' => 'Text',
            'options' => [
                'label' => 'Max time allowed'
            ],
            'attributes' => [
                'placeholder' => 'Enter maximum time allowed for the item'
            ]
        ]);

        // prerequisites
        $this->add([
            'name' => 'prerequisites',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Prerequisites'
            ],
            'attributes' => [
                'placeholder' => 'Enter pre-requisites for the item',
                'data-editable' => true
            ]
        ]);

        // time_limit_action
        $this->add([
            'name' => 'time_limit_action',
            'required' => true,
            'type' => 'Select',
            'options' => [
                'label' => 'Time limit action',
                'value_options' => [
                    'exit message',
                    'exit no message',
                    'continue message',
                    'continue no message'
                ]
            ]
        ]);

        // mastery_score
        $this->add([
            'name' => 'mastery_score',
            'type' => 'Text',
            'options' => [
                'label' => 'Mastery score'
            ],
            'attributes' => [
                'placeholder' => 'Enter mastery score for the item'
            ]
        ]);
    }
}