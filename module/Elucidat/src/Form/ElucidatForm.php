<?php

namespace Elucidat\Form;

use Savve\Form\AbstractForm;

class ElucidatForm extends AbstractForm
{

	/**
	 * Constructor
	 */
	public function __construct ($name = null, $options = [])
	{
		parent::__construct ($name, $options);

		// elucidat_id : hidden
		$this->add (['name' => 'id', 'type' => 'Hidden', 'attributes' => ['type' => 'hidden']]);


		// elucidat_id : public key
		$this->add (['name' => 'elucidat_public_key', 'type' => 'Hidden', 'attributes' => ['type' => 'hidden']]);

		// name
		$this->add (['name'       => 'company_name', 'options' => ['label' => 'Company name'],
					 'attributes' => ['placeholder' => 'Enter company name']]);
		// email
		$this->add (['name'       => 'company_email', 'options' => ['label' => 'Company email'],
					 'attributes' => ['placeholder' => 'Enter company email']]);


		// first name
		$this->add (['name'       => 'first_name', 'options' => ['label' => 'First name'],
					 'attributes' => ['placeholder' => 'Enter first name']]);
		// last name
		$this->add (['name'       => 'last_name', 'options' => ['label' => 'Last name'],
					 'attributes' => ['placeholder' => 'Enter last name']]);
		// telephone
		$this->add (['name'       => 'telephone', 'type' => 'Text', 'options' => ['label' => 'Telephone'],
					 'attributes' => ['placeholder' => 'Enter a telephone number']]);


		// street_address
		$this->add (['name'       => 'address1', 'type' => 'Text', 'options' => ['label' => 'Address1'],
					 'attributes' => ['placeholder' => 'Enter a address']]);
		// street_address 2
		$this->add (['name'       => 'address2', 'type' => 'Text', 'options' => ['label' => 'Address2'],
					 'attributes' => ['placeholder' => 'Enter a address']]);

		$this->add (['name'       => 'city', 'type' => 'Text', 'options' => ['label' => 'City'],
					 'attributes' => ['placeholder' => 'Enter city']]);

		// postcode
		$this->add (['name'       => 'postcode', 'type' => 'Text', 'options' => ['label' => 'Postcode'],
					 'attributes' => ['placeholder' => 'Enter postcode']]);
		// country
		$this->add (['name'    => 'country', 'type' => 'Select',
					 'options' => ['label'         => 'Country', 'empty_option' => 'Select country',
								   'value_options' => ['AU' => 'Australia', 'NZ' => 'New Zealand']]]);
		// status : select
		$this->add (['name'    => 'status', 'type' => 'Select', 'options' => ['label'         => 'Status',
																			  'value_options' => ['active'   => 'Active','inactive' => 'Inactive']]]);
		// elucidat_id : customer code : select : auto populated
		$this->add (['name' => 'elucidat_customer_code', 'type' => 'hidden', 'options' => ['label'         => 'Elucidat account details',
																						   'value_options' => [null   => 'Create new licence in elucidat']]]);
		// status : select
		$this->add (['name'    => 'site_id', 'type' => 'Select', 'options' => ['label' => 'Site Id', 'value_options' => ['100000'=>'Savvecentral.com']]]);

		// create new account in elucidat
		$this->add([
						   'name' => 'create_matching_account',
						   'type' => 'checkbox',
						   'options' => [
								   'label' => 'Create matching account in Elucidat',
								   'use_hidden_element' => true,
								   'checked_value' => '1',
								   'unchecked_value' => '0'
						   ]
				   ]);

		// submit
		$this->add (['name'    => 'submit', 'type' => 'Button', 'attributes' => ['type' => 'submit'],
					 'options' => ['label' => 'Save']]);
	}
}