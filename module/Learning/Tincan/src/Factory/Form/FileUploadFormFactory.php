<?php

namespace Tincan\Factory\Form;

use \ArrayObject as Object;
use Tincan\Validator\ManifestFileExists;
use Tincan\InputFilter\FileUploadInputFilter as InputFilter;
use Tincan\Hydrator\FileUploadAggregateHydrator as Hydrator;
use Tincan\Form\UploadFileForm as Form;
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

        // filter and validation
        $inputFileUpload = $inputFilter->get('file_upload');
        $filterChain = $inputFileUpload->getFilterChain();
        $validatorChain = $inputFileUpload->getValidatorChain();

        // attach the Tincan ManifestFileExists validator
        $validator = new ManifestFileExists();
        $validatorChain->attach($validator, true);
        $inputFilter->add($inputFileUpload);

        // form
        $form = new Form('tincan-file-upload');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'activity_id',
            'file_upload'
        ]);

        return $form;
    }
}