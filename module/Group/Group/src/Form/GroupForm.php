<?php

namespace Group\Form;

use Savve\Form\AbstractForm;

class GroupForm extends AbstractForm
{

    /**
     * Constructor
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // group_id : hidden
        $this->add([
            'name' => 'group_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
        ]);

        // parent_id : hidden
        $this->add([
            'name' => 'parent_id',
            'type' => 'Select',
            'options' => [
                'label' => 'Parent group',
                'empty_option' => 'Select parent group',
                'value_options' => []
            ]
        ]);

        // name
        $this->add([
            'name' => 'name',
            'options' => [
                'label' => 'Name'
            ],
            'attributes' => [
                'placeholder' => 'Enter group name'
            ]
        ]);

        // telephone
        $this->add([
            'name' => 'telephone',
            'type' => 'Text',
            'options' => [
                'label' => 'Telephone'
            ],
            'attributes' => [
                'placeholder' => 'Enter a telephone number'
            ]
        ]);

        // fax
        $this->add([
            'name' => 'fax',
            'type' => 'Text',
            'options' => [
                'label' => 'Fax'
            ],
            'attributes' => [
                'placeholder' => 'Enter a unique fax number'
            ]
        ]);

        // street_address
        $this->add([
            'name' => 'street_address',
            'type' => 'Text',
            'options' => [
                'label' => 'Street address'
            ],
            'attributes' => [
                'placeholder' => 'Enter a street address'
            ]
        ]);

        // suburb
        $this->add([
            'name' => 'suburb',
            'type' => 'Text',
            'options' => [
                'label' => 'Suburb'
            ],
            'attributes' => [
                'placeholder' => 'Enter suburb, town or city'
            ]
        ]);

        // postcode
        $this->add([
            'name' => 'postcode',
            'type' => 'Text',
            'options' => [
                'label' => 'Postcode'
            ],
            'attributes' => [
                'placeholder' => 'Enter postcode'
            ]
        ]);

        // state
        $this->add([
            'name' => 'state',
            'type' => 'Text',
            'options' => [
                'label' => 'State'
            ],
            'attributes' => [
                'placeholder' => 'Enter state, county or province'
            ]
        ]);

        // country
        $this->add([
            'name' => 'country',
            'type' => 'Select',
            'options' => [
                'label' => 'Country',
                'empty_option' => 'Select country',
                'value_options' => [
                    'AU' => 'Australia',
                    'NZ' => 'New Zealand',
                    'US' => 'United States of America'
                ]
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