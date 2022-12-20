<?php

namespace Scorm12\Factory\Form;

use \ArrayObject as Object;
use Scorm12\InputFilter\ItemInputFilter as InputFilter;
use Scorm12\Hydrator\AggregateHydrator as Hydrator;
use Scorm12\Form\ItemForm as Form;
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
        $form = new Form('update-scorm12-item');
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