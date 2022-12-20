<?php

namespace Group\Factory\Form;

use \ArrayObject as Object;
use Group\InputFilter\Group as InputFilter;
use Group\Hydrator\AggregateHydrator as Hydrator;
use Group\Form\GroupForm as Form;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UpdateGroupFormFactory implements
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

        // form instance
        $form = new Form('update-group');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // validation group
        $form->setValidationGroup([
            'group_id',
            'name',
            'telephone',
            'fax',
            'street_address',
            'suburb',
            'postcode',
            'state',
            'country',
            'parent_id'
        ]);

        return $form;
    }
}