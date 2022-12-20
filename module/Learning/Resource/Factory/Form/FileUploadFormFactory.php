<?php

namespace Resource\Factory\Form;

use \ArrayObject as Object;
use Resource\InputFilter\FileUploadInputFilter as InputFilter;
use Resource\Hydrator\AggregateHydrator as Hydrator;
use Resource\Form\UploadFileForm as Form;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Filter\File as ZendFileFilter;
use Zend\Validator\File as ZendFileValidator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FileUploadFormFactory implements
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
        $serviceManager = $serviceLocator->getServiceLocator();

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        /* @var $filterChain \Zend\Filter\FilterChain */
        /* @var $validatorChain \Zend\Validator\ValidatorChain */

        // $validatorManager = $serviceManager->get('ValidatorManager');
        // $validator = new \Resource\Validator\Uploaded($validatorManager);

        // filter and validation
        // $inputFileUpload = $inputFilter->get('file_upload');
        // $filterChain = $inputFileUpload->getFilterChain();
        // $validatorChain = $inputFileUpload->getValidatorChain();
        // $validatorChain->attach($validator, true);
        // $inputFilter->add($inputFileUpload);

        // $inputUrl = $inputFilter->get('url');
        // $filterChain = $inputUrl->getFilterChain();
        // $validatorChain = $inputUrl->getValidatorChain();
        // $validatorChain->attach($validator, true);

        // form
        $form = new Form('resource-file-upload');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'activity_id',
            'file_upload',
            'title',
            'url'
        ]);

        return $form;
    }
}