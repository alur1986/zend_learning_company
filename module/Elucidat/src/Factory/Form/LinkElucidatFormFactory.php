<?php

namespace Elucidat\Factory\Form;

use \ArrayObject as Object;
use Elucidat\InputFilter\Elucidat as InputFilter;
use Elucidat\Hydrator\AggregateHydrator as Hydrator;
use Elucidat\Form\ElucidatForm as Form;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LinkElucidatFormFactory implements FactoryInterface
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
		$form = new Form('link-elucidat');
		$form->setHydrator ($hydrator);
		$form->setInputFilter ($inputFilter);
		$form->setObject ($object);

		// validation elucidat
		$form->setValidationGroup (['status', 'site_id']);

		return $form;
	}
}