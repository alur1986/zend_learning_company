<?php

namespace Authorization\Factory\Form\Resource;

use \ArrayObject as Object;
use Authorization\Hydrator\Resource\AggregateHydrator as Hydrator;
use Authorization\InputFilter\Resource\InputFilter;
use Authorization\Form\Resource\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateFormFactory implements
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
        $form = new Form('authorization-resource-create');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // validation
        $form->setValidationGroup([
            'resource',
            'title',
            'description',
            'type',
            'level_id'
        ]);

        return $form;
    }
}