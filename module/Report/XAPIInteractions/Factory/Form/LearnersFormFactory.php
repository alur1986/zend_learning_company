<?php

namespace Report\XAPIInteractions\Factory\Form;

use \ArrayObject as Object;
use Report\XAPIInteractions\InputFilter\InputFilter as InputFilter;
use Report\XAPIInteractions\Hydrator\AggregateHydrator as Hydrator;
use Report\XAPIInteractions\Form\LearnersForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnersFormFactory implements
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
        $form = new Form('report-xapi-interactions');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'learner_id'
        ]);

        return $form;
    }
}