<?php

namespace Learner\Factory\Form;

use Learner\Form\Learner as Form;
use Learner\InputFilter\Learner as InputFilter;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use \ArrayObject as Entity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class PasswordFormFactory implements
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
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        $inputFilter = new InputFilter();
        $hydrator = new Hydrator();
        $object = new Entity();

        // form
        $form = new Form('change-password');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set validation group
        $form->setValidationGroup([
            'user_id',
            'new_password',
            'confirm_password'
        ]);

        return $form;
    }
}