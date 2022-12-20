<?php

namespace Company\Form;

use Savve\Form\AbstractForm;

class CompanyForm extends AbstractForm
{

    /**
     * Constructor
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // company_id : hidden
        $this->add([
            'name' => 'company_id',
            'type' => 'Hidden',
            'attributes' => [
                'type' => 'hidden'
            ]
        ]);

        // name
        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'options' => [
                'label' => 'Company name'
            ],
            'attributes' => [
                'placeholder' => 'Enter a company name, eg Acme Pty Ltd'
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
                'placeholder' => 'Enter company telephone number'
            ]
        ]);

        // fax
        $this->add([
            'name' => 'fax',
            'type' => 'Text',
            'options' => [
                'label' => 'Fax number'
            ],
            'attributes' => [
                'placeholder' => 'Enter a fax number'
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
                'placeholder' => 'Enter street address'
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
                'empty_option' => "Select a country"
            ]
        ]);

        // website
        $this->add([
            'name' => 'website',
            'type' => 'Text',
            'options' => [
                'label' => 'Company website',
                'prefix' => '<span class="add-on">http://</span>'
            ],
            'attributes' => [
                'placeholder' => 'Enter company website URL'
            ]
        ]);

        // abn
        $this->add([
            'name' => 'abn',
            'type' => 'Text',
            'options' => [
                'label' => 'ABN'
            ],
            'attributes' => [
                'placeholder' => 'Enter your Australian Business Number (ABN)'
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