<?php

namespace Report\MyExperience\Factory\Form;

use \ArrayObject as Object;
use Report\MyExperience\InputFilter\FilterInputFilter as InputFilter;
use Report\MyExperience\Hydrator\AggregateHydrator as Hydrator;
use Report\MyExperience\Form\FilterForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterUpdateFormFactory implements
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
        $form = new Form('report-myexperience-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set default value
        $form->get('title')
            ->setValue('MyExperience xAPI Report');

        // form validation group
        $form->setValidationGroup([
            'filter_id',
            'title',
            'description'
        ]);

        return $form;
    }
}