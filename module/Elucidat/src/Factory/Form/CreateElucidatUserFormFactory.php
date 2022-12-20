<?php

namespace Elucidat\Factory\Form;

use \ArrayObject as Object;
use Elucidat\InputFilter\ElucidatUser as InputFilter;
use Elucidat\Hydrator\AggregateHydrator as Hydrator;
use Elucidat\Form\ElucidatUserForm as Form;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CreateElucidatUserFormFactory implements FactoryInterface
{

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 *
	 * @return mixed
	 */
	public function createService (ServiceLocatorInterface $serviceLocator)
	{
		$hydrator = new Hydrator();
		$inputFilter = new InputFilter();
		$object = new Object();

		// form instance
		$form = new Form('create-elucidat-user');
		$form->setHydrator ($hydrator);
		$form->setInputFilter ($inputFilter);
		$form->setObject ($object);

		// validation elucidat
		$form->setValidationGroup (['user_id','elucidat_email','has_elucidat_access']);

		return $form;
	}
}