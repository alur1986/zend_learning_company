<?php

namespace Learner\Factory\Form;

use Learner\Validator\MobileNumberExists;
use Learner\Validator\EmailAddressExists;
use Learner\Validator\EmploymentIdExists;
use Learner\Form\Learner as Form;
use Savve\Factory\AbstractFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorInterface;

class SettingsFormFactory extends AbstractFactory
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

        // inject hydrator
        $hydratorManager = $serviceManager->get('HydratorManager');
        $hydrator = $hydratorManager->get('Learner\Hydrator\Learner');

        // inject inputFilter
        $inputFilterManager = $serviceManager->get('InputFilterManager');
        $inputFilter = $inputFilterManager->get('Learner\InputFilter\Learner');

        // instantiate form
        $form = new Form('learner');
        $form->setLabel('Edit Learner');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);

        // set validation group
        $form->setValidationGroup([
            'user_id',
            'timezone',
            'locale'
        ]);

        return $form;
    }
}