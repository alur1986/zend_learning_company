<?php

namespace Report\MyLocker\Factory\Form;

use \ArrayObject as Object;
use Report\MyLocker\InputFilter\InputFilter as InputFilter;
use Report\MyLocker\Hydrator\AggregateHydrator as Hydrator;
use Report\MyLocker\Form\GroupsForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GroupsFormFactory implements
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
        $form = new Form('report-mylocker');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'group_id'
        ]);

        return $form;
    }
}