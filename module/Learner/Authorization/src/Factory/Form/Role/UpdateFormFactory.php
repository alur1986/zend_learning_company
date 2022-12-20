<?php

namespace Authorization\Factory\Form\Role;

use \ArrayObject as Object;
use Authorization\Hydrator\Role\AggregateHydrator as Hydrator;
use Authorization\InputFilter\Role\InputFilter;
use Authorization\Form\Role\Form;
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

        // form
        $form = new Form('authorization-role-create');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // modify
        $form->get('name')
            ->setAttribute('disabled', 'disabled');

        // validation
        $form->setValidationGroup([
            'id',
            'title',
            'description',
            'level_id'
        ]);

        return $form;
    }
}