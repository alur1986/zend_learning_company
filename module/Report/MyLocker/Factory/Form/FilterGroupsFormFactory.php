<?php

namespace Report\MyLocker\Factory\Form;

use \ArrayObject as Object;
use Report\MyLocker\InputFilter\FilterInputFilter as InputFilter;
use Report\MyLocker\Hydrator\AggregateHydrator as Hydrator;
use Report\MyLocker\Form\FilterGroupsForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterGroupsFormFactory implements
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
        $form = new Form('report-mylocker-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'filter_id',
            'group_id'
        ]);

        return $form;
    }
}