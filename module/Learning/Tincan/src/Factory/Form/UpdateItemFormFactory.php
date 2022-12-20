<?php

namespace Tincan\Factory\Form;

use \ArrayObject as Object;
use Tincan\InputFilter\ItemInputFilter as InputFilter;
use Tincan\Hydrator\AggregateHydrator as Hydrator;
use Tincan\Form\ItemForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class UpdateItemFormFactory implements
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
        $serviceManager = $serviceLocator->getServiceLocator();

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // form
        $form = new Form('update-tincan-item');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'item_id',
            'title',
            'identifier',
            'item_location',
            'prerequisites',
            'mastery_score'
        ]);

        return $form;
    }
}