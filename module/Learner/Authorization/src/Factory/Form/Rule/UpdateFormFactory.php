<?php

namespace Authorization\Factory\Form\Rule;

use \ArrayObject as Object;
use Authorization\Hydrator\Rule\AggregateHydrator as Hydrator;
use Authorization\InputFilter\Rule\InputFilter;
use Authorization\Form\Rule\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class UpdateFormFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();
        $form = new Form('authorization-rule-update');

        // modify some elements
        $form->get('role_id')
            ->setAttribute('disabled', 'disabled');
        $form->get('resource_id')
            ->setAttribute('disabled', 'disabled');
        $inputFilter->get('role_id')
            ->setRequired(false);
        $inputFilter->get('resource_id')
            ->setRequired(false);

        // set
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // validation
        $form->setValidationGroup([
            'site_id',
            'role_id',
            'resource_id',
            'permission'
        ]);

        return $form;
    }
}