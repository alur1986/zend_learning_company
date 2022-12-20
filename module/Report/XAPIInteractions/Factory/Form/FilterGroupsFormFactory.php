<?php

namespace Report\XAPIInteractions\Factory\Form;

use \ArrayObject as Object;
use Report\XAPIInteractions\InputFilter\FilterInputFilter as InputFilter;
use Report\XAPIInteractions\Hydrator\AggregateHydrator as Hydrator;
use Report\XAPIInteractions\Form\FilterForm as Form;
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
        $form = new Form('report-xapi-interactions-filter');
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