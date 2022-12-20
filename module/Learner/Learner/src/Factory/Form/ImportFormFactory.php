<?php

namespace Learner\Factory\Form;

use Learner\Validator\HeaderRowExists;
use Learner\Form\ImportForm as Form;
use Learner\InputFilter\Import as InputFilter;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use \ArrayObject as Object;
use Savve\Factory\AbstractFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImportFormFactory extends AbstractFactory
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
        $object = new Object();

        // add validators
        // $input = $inputFilter->get('file_upload');
        // $validatorChain = $input->getValidatorChain();
        // $validatorChain->attach(new HeaderRowExists(['fieldNames' => 'first_name,last_name,email,mobile_number,employment_id']), true);
        // $inputFilter->add($input);

        // form
        $form = new Form('bulk_upload');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // validation group
        $form->setValidationGroup([
            'file_upload'
        ]);

        return $form;
    }
}