<?php

namespace Learning\Form;

use Savve\Form\AbstractForm;

class LearningForm extends AbstractForm
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

        // activity_type
        $this->add([
            'name' => 'activity_type',
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
            'type' => 'Text',
            'options' => [
                'label' => 'Activity Title'
            ],
            'attributes' => [
                'placeholder' => 'Enter title of learning activity'
            ]
        ]);

        // description
        $this->add([
            'name' => 'description',
            'required' => true,
            'options' => [
                'label' => 'Description'
            ],
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter description of learning activity',
                'data-editable' => true
            ]
        ]);

        // learning_objective
        $this->add([
            'name' => 'learning_objective',
            'required' => true,
            'options' => [
                'label' => 'Learning objectives'
            ],
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter learning activity objective description',
                'data-editable' => true
            ]
        ]);

        // prerequisites
        $this->add([
            'name' => 'prerequisites',
            'options' => [
                'label' => 'Pre-requisites'
            ],
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter learning activity pre-requisite description',
                'data-editable' => true
            ]
        ]);

        // version
        $this->add([
            'name' => 'version',
            'options' => [
                'label' => 'Version'
            ],
            'attributes' => [
                'placeholder' => 'Version'
            ]
        ]);

        // code
        $this->add([
            'name' => 'code',
            'type' => 'Text',
            'options' => [
                'label' => 'Identifier'
            ],
            'attributes' => [
                'placeholder' => 'Enter the code of the learning activity'
            ]
        ]);

        // duration
        $this->add([
            'name' => 'duration',
            'type' => 'Text',
            'options' => [
                'label' => 'Course duration (in minutes)'
            ],
            'attributes' => [
                'placeholder' => 'Enter duration in minutes'
            ]
        ]);

        // direct_cost
        $this->add([
            'name' => 'direct_cost',
            'type' => 'Text',
            'options' => [
                'label' => 'Direct cost'
            ]
        ]);

        // indirect_cost
        $this->add([
            'name' => 'indirect_cost',
            'type' => 'Text',
            'options' => [
                'label' => 'Indirect cost'
            ]
        ]);

        // cpd
        $this->add([
            'name' => 'cpd',
            'type' => 'Text',
            'options' => [
                'label' => 'CPD points'
            ]
        ]);

        // category_id
        $this->add([
            'name' => 'category_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Category',
                'empty_option' => 'Uncategorised',
                'value_options' => []
            ]
        ]);

        // keywords
        $this->add([
            'name' => 'keywords',
            'type' => 'Text',
            'attributes' => [
                'placeholder' => 'Enter keywords seperated with comma.',
                'data-max-items' => 5,
                'data-filter' => 'tag',
                'data-can-create' => true,
                'data-taxonomy' => true
            ],
            'options' => [
                'label' => 'Keywords'
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

        // priority
        $range = range(1, 5);
        $values = array_combine($range, $range);
        $this->add([
            'name' => 'priority',
            'type' => 'Select',
            'attributes' => [
                'placeholder' => 'Priority'
            ],
            'options' => [
                'label' => 'Priority',
                'value_options' => $values
            ]
        ]);

        // catalog_description
        $this->add([
            'name' => 'catalog_description',
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => 'Enter learning activity catalogue description',
                'data-editable' => true
            ],
            'options' => [
                'label' => 'Catalogue description'
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
                'label' => 'Catalogue thumbnail image (File extension: *.jpg or *.jpeg -- Recommended file size: 276 x 210 pixels / 7 x 5.3 cm)',
                'data-gallery' => true
            ]
        ]);

        // catalogue_image
        $this->add([
            'name' => 'catalog_image',
            'required' => true,
            'attributes' => [
                'placeholder' => 'Catalogue image (File extension: *.jpg or *.jpeg -- Recommended file size: 276 x 210 pixels / 7 x 5.3 cm)',
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

        // date_created
        $this->add([
            'name' => 'date_created',
            'attributes' => [
                'placeholder' => 'Date created',
                'disabled' => 'disabled'
            ],
            'options' => [
                'label' => 'Date created'
            ]
        ]);

        // date_edited
        $this->add([
            'name' => 'date_edited',
            'attributes' => [
                'placeholder' => 'Date edited',
                'disabled' => 'disabled'
            ],
            'options' => [
                'label' => 'Date edited'
            ]
        ]);

        // auto_approve
        $this->add([
            'name' => 'auto_approve',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Auto-approve',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // needs_enrolment
        $this->add([
            'name' => 'needs_enrolment',
            'type' => 'checkbox',
            'options' => [
                'label' => "Needs 'Enrol' button",
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        // distribution settings
        // auto_distribute
        $this->add([
            'name' => 'auto_distribute',
            'type' => 'checkbox',
            'options' => [
                'label' => "Auto Distribute",
                'checked_value' => '1'
            ],
            'attributes' => [
                'title' => 'Enable automatic activity distribution - you Must select either Distribute on Registration or Distribute on Login for this functionality to take affect'
            ]
        ]);

        // if auto distribute is ON
        // auto_distribute_on_registration
        $this->add([
            'name' => 'auto_distribute_on_registration',
            'type' => 'checkbox',
            'options' => [
                'label' => "Distribute on Registration (for New learners)",
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ],
            'attributes' => [
                'title' => 'This option will ensure that this activity is distributed to any new learners after they complete the registration process'
            ]
        ]);

        // if auto distribute is ON
        // auto_distribute_on_login
        $this->add([
            'name' => 'auto_distribute_on_login',
            'type' => 'checkbox',
            'options' => [
                'label' => "Distribute on Login (for Current learners)",
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ],
            'attributes' => [
                'title' => 'This option will ensure that this activity is distributed to all learners when they login (it will only distribute the activity once - cannot be used form redistribution)'
            ]
        ]);

        // if auto distribute is ON
        // ordering
        $range = range(0, 10);
        $values = array_combine($range, $range);
        $this->add([
            'name' => 'ordering',
            'type' => 'Select',
            'options' => [
                'label' => 'Distribution Ordering (requires a Learning Playlist group)',
                'empty_option' => 'Select an Order for Distribution',
                'value_options' => $values
            ],
            'attributes' => [
                'title' => 'To enable a Staggered Distribution of activities within a Learning Playlist group, set the order in which you want them to be distributed (0 is lowest through to the highest selected) - then select a Distribution Delay (the delay can be set individually for each activity). A Learning Playlist must be created before using this feature'
            ]
        ]);

        // if auto distribute is ON
        // auto_distribute_delay
        $range = range(0, 14);
        $values = array_combine($range, $range);
        $this->add([
            'name' => 'auto_distribute_delay',
            'type' => 'Select',
            'options' => [
                'label' => 'Distribution Delay (works with or without a Learning Playlist group)',
                'empty_option' => 'Distribution Delay (0 -> 14 days, based on Ordering values)',
                'value_options' => $values
            ],
            'attributes' => [
                'title' => 'When this value is Unset or set to Zero this activity will be distributed immediately upon Login or Registration (you can stagger the delay period between activity distribution within a Learning Playlist group. ex: 2nd activity distributes 1 day after, 3rd activity distributes 3 days after etc)'
            ]
        ]);

        // if activity is part of a learning plan (group)
        // plan_id
        $this->add([
            'name' => 'plan_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Learning Playlist (grouping activities)',
                'empty_option' => 'Select a Learning Playlist to group activities',
                'value_options' => [],

            ],
            'attributes' => [
                'title' => 'You can use Learning Playlists to group activities together - If you want to use Automatic Distribution of multiple activities and have staggered (delayed) distribution of activities managed by the Ordering value then your MUST place them into a Learning Playlist group',
                'multiple' => true
            ]
        ]);

        // if activity is part of a learning plan (group)
        // prerequisite
        $this->add([
            'name' => 'prerequisite',
            'type' => 'Select',
            'options' => [
                'label' => 'Prerequisite (required activity)',
                'empty_option' => 'Select an activity which must be completed prior to beginning this activity',
                'value_options' => [],

            ],
            'attributes' => [
                'title' => 'Selecting an activity here will prevent this activity from being \'launched\' until the selected activity has been completed. Only available for Learning Playlist activities'
            ]
        ]);

        // stores any of the 'above' and used to help populate the selectors with the correctly 'selected' values
        // selected_prerequisite
        $this->add([
            'name' => 'selected_prerequisite',
            'type' => 'Hidden'
        ]);

        // licensed
        $this->add([
            'name' => 'licensed',
            'type' => 'Number',
            'attributes' => [
                'placeholder' => 'If applicable enter the max number of Learner Distributions allowed',
                'min' => 1,
                'max' => '9999',
                'maxlength' => 4
            ],
            'options' => [
                'label' => 'Licensed Activity (maximum no. of distributions)'
            ]
        ]);


        // submit
        $this->add([
            'name' => 'submit',
            'type' => 'Button',
            'attributes' => [
                'type' => 'submit'
            ],
            'options' => [
                'label' => 'Save'
            ]
        ]);
    }
}