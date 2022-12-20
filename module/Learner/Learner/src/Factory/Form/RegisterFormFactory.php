<?php

namespace Learner\Factory\Form;

use Learner\Validator\MobileNumberExists;
use Learner\Validator\EmailAddressExists;
use Learner\Validator\EmploymentIdExists;
use Learner\Form\Learner as Form;
use Learner\InputFilter\Learner as InputFilter;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use \ArrayObject as Entity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class RegisterFormFactory implements
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

        // validator
        $validatorManager = $serviceManager->get('ValidatorManager');

        /* @var $validatorChain \Zend\Validator\ValidatorChain */

        // add validators to the 'email'
        /*$validator = new EmailAddressExists($validatorManager);
        $inputEmail = $inputFilter->get('email');
        $validatorChain = $inputEmail->getValidatorChain()
            ->attach($validator, true);
        $inputFilter->add($inputEmail);

        // add validators to the 'mobile_number'
        $validator = new MobileNumberExists($validatorManager);
        $inputMobileNumber = $inputFilter->get('mobile_number');
        $validatorChain = $inputMobileNumber->getValidatorChain()
            ->attach($validator, true);
        $inputFilter->add($inputMobileNumber);

        // add validator to the 'employment_id'
        $inputEmploymentId = $inputFilter->get('employment_id');
        $validator = new EmploymentIdExists($validatorManager);
        $validatorChain = $inputEmploymentId->getValidatorChain();
        $validatorChain->attach($validator, true);
        $inputFilter->add($inputEmploymentId); */

        // process form post
        /* @var $post \Zend\Stdlib\Parameters */
        $post = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRequest()
            ->getPost()
            ->toArray();

    	if (!empty($post)) {
            if ((isset($post['email']) && empty($post['email'])) && (isset($post['mobile_number']) && empty($post['mobile_number'])) && (isset($post['employment_id']) && empty($post['employment_id']))) {
                $inputFilter->get('email')->setRequired(true);
                $inputFilter->get('mobile_number')->setRequired(true);
                $inputFilter->get('employment_id')->setRequired(true);
            }
            if ((isset($post['agent_code']) && empty($post['agent_code'] )) || (isset($post['agent_password']) && empty($post['agent_password'] )) || (isset($post['start_date']) && empty($post['start_date'] )) || (isset($post['course_selector']) && empty($post['course_selector'] ))) {
                $inputFilter->get('agent_code')->setRequired(true);
                $inputFilter->get('agent_password')->setRequired(true);
                $inputFilter->get('start_date')->setRequired(true);
                $inputFilter->get('course_selector')->setRequired(true);
            }
        }

        // form
        $form = new Form('register');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'first_name',
            'last_name',
            'email',
            'mobile_number',
            'street_address',
            'new_password',
            'confirm_password',
            'employment_id',
            'postcode',
            'group_id',
            'cpd_id',
            'cpd_number',
            'note',
            'referrer',
            'subscription',
            'agent_code',
            'agent_password',
            'agent_email',
            'start_date',
            'course_selector'
        ]);

        return $form;
    }
}