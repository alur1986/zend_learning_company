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

class CreateFormFactory implements
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
        $inputEmail = $inputFilter->get('email');
        $validatorChain = $inputEmail->getValidatorChain();
        $validatorChain->attach((new EmailAddressExists($validatorManager)), true);
        $inputFilter->add($inputEmail);

        // add validators to the 'mobile_number'
        $inputMobileNumber = $inputFilter->get('mobile_number');
        $validatorChain = $inputMobileNumber->getValidatorChain();
        $validatorChain->attach((new MobileNumberExists($validatorManager)), true);
        $inputFilter->add($inputMobileNumber);

        // add validator to the 'employment_id'
        $inputEmploymentId = $inputFilter->get('employment_id');
        $validatorChain = $inputEmploymentId->getValidatorChain();
        $validatorChain->attach((new EmploymentIdExists($validatorManager)), true);
        $inputFilter->add($inputEmploymentId);

        // process form post
        /* @var $post \Zend\Stdlib\Parameters */
        $post = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRequest()
            ->getPost()
            ->toArray();
    	if (!empty($post)) {
            if ((isset($post['email']) && empty($post['email'])) && (isset($post['mobile_number']) && empty($post['mobile_number']))) {
       //         $inputFilter->get('email')->setRequired(true);
       //         $inputFilter->get('mobile_number')->setRequired(true);
                $inputFilter->get('employment_id')->setRequired(true);
            }
        }

        // form
        $form = new Form('create-learner');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'first_name',
            'last_name',
            'email',
            'telephone',
            'mobile_number',
            'street_address',
            'suburb',
            'postcode',
            'state',
            'country',
            'gender',
            'new_password',
            'confirm_password',
            'employment_id',
            'cpd_id',
            'cpd_number',
            'note',
            'referrer',
            'subscription'
        ]);

        return $form;
    }
}