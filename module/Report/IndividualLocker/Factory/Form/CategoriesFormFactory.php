<?php

namespace Report\IndividualLocker\Factory\Form;

use \ArrayObject as Object;
use Report\IndividualLocker\InputFilter\InputFilter as InputFilter;
use Report\IndividualLocker\Hydrator\AggregateHydrator as Hydrator;
use Report\IndividualLocker\Form\CategoriesForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CategoriesFormFactory implements
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
        $form = new Form('report-individual-locker');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'category_id'
        ]);

        return $form;
    }
}