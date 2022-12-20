<?php

namespace Report\IndividualLearning\Factory\Form;

use \ArrayObject as Object;
use Report\IndividualLearning\InputFilter\InputFilter as InputFilter;
use Report\IndividualLearning\Hydrator\AggregateHydrator as Hydrator;
use Report\IndividualLearning\Form\GroupsForm as Form;
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
        $form = new Form('report-individual-learning');
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