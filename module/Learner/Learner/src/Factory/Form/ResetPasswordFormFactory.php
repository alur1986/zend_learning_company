<?php

namespace Learner\Factory\Form;

use \ArrayObject as Object;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use Learner\InputFilter\Learner as InputFilter;
use Learner\Form\Learner as Form;
use Savve\Factory\AbstractFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResetPasswordFormFactory extends AbstractFactory
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

        // instantiate form
        $form = new Form('learner');
        $form->setLabel('Reset Password');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set validation group
        $form->setValidationGroup(array(
            'password_token',
            'new_password',
            'confirm_password'
        ));

        return $form;
    }
}