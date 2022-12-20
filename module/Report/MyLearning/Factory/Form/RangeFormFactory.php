<?php

namespace Report\MyLearning\Factory\Form;

use \ArrayObject as Object;
use Report\MyLearning\InputFilter\InputFilter as InputFilter;
use Report\MyLearning\Hydrator\AggregateHydrator as Hydrator;
use Report\MyLearning\Form\RangeForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class RangeFormFactory implements
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
        $form = new Form('report-mylearning');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'show_from',
            'show_to',
            'all_dates',
            'tracking_status',
            'learner_status'
        ]);

        return $form;
    }
}