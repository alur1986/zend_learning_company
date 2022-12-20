<?php

namespace Report\MyLearning\Factory\Form;

use \ArrayObject as Object;
use Report\MyLearning\InputFilter\FilterInputFilter as InputFilter;
use Report\MyLearning\Hydrator\AggregateHydrator as Hydrator;
use Report\MyLearning\Form\FilterRangeForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterRangeFormFactory implements
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
        $form = new Form('report-mylearning-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'filter_id',
            'show_from',
            'show_to',
            'all_dates',
            'tracking_status',
            'learner_status'
        ]);

        return $form;
    }
}