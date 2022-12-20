<?php

namespace Group\Learner\Factory\Form;

use \ArrayObject as Object;
use Group\Learner\Hydrator\AggregateHydrator as Hydrator;
use Group\Learner\InputFilter\InputFilter as InputFilter;
use Group\Learner\Form\ImportForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ImportFormFactory implements
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
        $form = new Form('group-learner-import');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'file_upload'
        ]);

        return $form;
    }
}