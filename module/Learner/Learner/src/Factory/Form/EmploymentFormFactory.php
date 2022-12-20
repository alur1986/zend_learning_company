<?php

namespace Learner\Factory\Form;

use \ArrayObject as Object;
use Learner\InputFilter\Employment as InputFilter;
use Learner\Hydrator\Employment as Hydrator;
use Learner\Form\Employment as Form;
use Learner\Validator\MobileNumberExists;
use Learner\Validator\EmailAddressExists;
use Learner\Validator\EmploymentIdExists;
use Savve\Factory\AbstractFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmploymentFormFactory extends AbstractFactory
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

        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        // validator
        $validatorManager = $serviceManager->get('ValidatorManager');

        // add validator to the 'employment_id'
        $inputEmploymentId = $inputFilter->get('employment_id');
        $validator = new EmploymentIdExists($validatorManager);
        $validatorChain = $inputEmploymentId->getValidatorChain();
        $validatorChain->attach($validator, true);
        $inputFilter->add($inputEmploymentId);

        // instantiate form
        $form = new Form('employment');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set validation group
        $form->setValidationGroup([
            'user_id',
            'employment_id',
            'employment_type',
            'start_date',
            'end_date',
            'cost_centre',
            'location',
            'position',
            'manager'
        ]);

        return $form;
    }
}