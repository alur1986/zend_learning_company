<?php

namespace Learner\Factory\Form;

use \ArrayObject as Entity;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use Learner\InputFilter\PhotoInputFilter as InputFilter;
use Learner\Form\PhotoForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class PhotoFormFactory implements
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
        $object = new Entity();

        // form
        $form = new Form('learner-photo');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'user_id',
            'profile_photo'
        ]);

        return $form;
    }
}