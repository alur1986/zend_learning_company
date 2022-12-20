<?php

namespace LearningPlan\Form;

use Savve\Form\AbstractForm;

class LearningPlanForm extends AbstractForm
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

        // title
        $this->add([
            'name' => 'title',
            'required' => true,
            'type' => 'Text',
            'options' => [
                'label' => 'Learning Playlist Title'
            ],
            'attributes' => [
                'placeholder' => 'Enter a title for this Learning Playlist'
            ]
        ]);

        // description
        $this->add([
            'name' => 'description',
            'required' => false,
            'options' => [
                'label' => 'Playlist Description (image Source example: /uploads/file-name-of-image.jpg - the application may automatically rewrite the path after completion)'
            ],
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter a description for this Learning Playlist - images must first be uploaded to the Image Galley',
                'data-editable' => true
            ]
        ]);

        // catalogue_thumb
        $this->add([
            'name' => 'catalog_thumb',
            'required' => true,
            'attributes' => [
                'placeholder' => 'Catalogue thumb',
                'data-gallery' => '1'
            ],
            'options' => [
                'label' => 'Catalogue thumbnail image',
                'data-gallery' => true
            ]
        ]);

        // catalogue_image
        $this->add([
            'name' => 'catalog_image',
            'required' => true,
            'attributes' => [
                'placeholder' => 'Catalogue image',
                'data-gallery' => '1'
            ],
            'options' => [
                'label' => 'Catalogue image',
                'data-gallery' => true
            ]
        ]);

        // catalog_display
        $this->add([
            'name' => 'catalog_display',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Show in catalogue',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // status
        $this->add([
            'name' => 'status',
            'required' => true,
            'type' => 'Select',
            'attributes' => [
                'placeholder' => 'Status'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ]
            ]
        ]);

    }
}