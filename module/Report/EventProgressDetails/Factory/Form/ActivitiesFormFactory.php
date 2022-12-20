<?php

namespace Report\EventProgressDetails\Factory\Form;

use \ArrayObject as Object;
use Report\EventProgressDetails\InputFilter\InputFilter as InputFilter;
use Report\EventProgressDetails\Hydrator\AggregateHydrator as Hydrator;
use Report\EventProgressDetails\Form\ActivitiesForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ActivitiesFormFactory implements
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
        $form = new Form('report-event-progress-details');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'activity_id'
        ]);

        return $form;
    }
}