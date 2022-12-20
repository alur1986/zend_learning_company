<?php

namespace Report\IndividualLearning\Factory\Form;

use \ArrayObject as Object;
use Report\IndividualLearning\InputFilter\FilterInputFilter as InputFilter;
use Report\IndividualLearning\Hydrator\AggregateHydrator as Hydrator;
use Report\IndividualLearning\Form\FilterForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterCreateFormFactory implements
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
        $form = new Form('report-individual-learning-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set default value
        $form->get('title')
            ->setValue('Event Progress Details Report');

        // form validation group
        $form->setValidationGroup([
            'title',
            'description'
        ]);

        return $form;
    }
}