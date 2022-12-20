<?php

namespace Report\IndividualLocker\Factory\Form;

use \ArrayObject as Object;
use Report\IndividualLocker\InputFilter\FilterInputFilter as InputFilter;
use Report\IndividualLocker\Hydrator\AggregateHydrator as Hydrator;
use Report\IndividualLocker\Form\FilterLearnersForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterLearnersFormFactory implements
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
        $form = new Form('report-individual-locker-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'filter_id',
            'learner_id'
        ]);

        return $form;
    }
}