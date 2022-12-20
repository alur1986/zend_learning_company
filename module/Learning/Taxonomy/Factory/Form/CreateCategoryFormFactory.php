<?php

namespace Learning\Taxonomy\Factory\Form;

use \ArrayObject as Object;
use Learning\Taxonomy\Form\TaxonomyForm as Form;
use Learning\Taxonomy\InputFilter\InputFilter as InputFilter;
use Learning\Taxonomy\Hydrator\AggregateHydrator as Hydrator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateCategoryFormFactory implements
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
        $form = new Form('learning-taxonomy');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation
        $form->setValidationGroup([
            'term',
            'description',
            'parent_id'
        ]);

        return $form;
    }
}