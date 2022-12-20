<?php

namespace Report\Interactions\Factory\Form;

use Report\Interactions\InputFilter\InputFilter as InputFilter;
use Report\Interactions\Form\IndexForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class IndexFormFactory implements
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
        $inputFilter = new InputFilter();

        // form
        $form = new Form('report-interactions');
        $form->setInputFilter($inputFilter);

        return $form;
    }
}
