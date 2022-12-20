<?php

namespace Group\Learner\Factory\Form;

use \ArrayObject as Object;
use Group\Learner\Form\AddLearnerToGroupForm as Form;
use Group\Learner\Hydrator\AggregateHydrator as Hydrator;
use Group\Learner\InputFilter\InputFilter as InputFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AddLearnerToGroupFormFactory implements
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

        $form = new Form('add-learner-to-group');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);
        $form->setValidationGroup([
            'group_id',
            'learner_id'
        ]);

        return $form;
    }
}