<?php

namespace Elucidat\Form;

use Savve\Form\AbstractForm;

class ElucidatUserForm extends AbstractForm
{

	/**
	 * Constructor
	 */
	public function __construct ($name = null, $options = [])
	{
		parent::__construct ($name, $options);

		// elucidat_id : hidden
		$this->add (['name' => 'id', 'type' => 'Hidden', 'attributes' => ['type' => 'hidden']]);

		// account id : select
		$this->add (['name'    => 'account_id', 'type' => 'Hidden', 'attributes' => ['type' => 'hidden']]);

		// user
		$this->add (['name'    => 'user_id', 'type' => 'Select',
					 'options' => ['label'         => 'Learner Id', 'value_options' => ['100000'=>'User']],
						'attributes' => ['block-load'=>1]
					]
		);

		// email
		$this->add (['name'       => 'elucidat_email', 'options' => ['label' => 'Elucidat email'],
					 'attributes' => ['placeholder' => 'Enter company email']]);

		// has elucidat access : select
		$this->add (['name'    => 'has_elucidat_access', 'type' => 'Select', 'options' => ['label'         => 'Has Elucidat Access', 'value_options' => [0   => 'No',1 => 'Yes']]]);


		$this->add (['name'    => 'submit', 'type' => 'Button', 'attributes' => ['type' => 'submit'],
					 'options' => ['label' => 'Save']]);
	}
}