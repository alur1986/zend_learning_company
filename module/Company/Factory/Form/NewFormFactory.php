<?php

namespace Company\Factory\Form;

use Savvecentral\Entity\Company as Entity;
use Company\InputFilter\Company as InputFilter;
use Company\Hydrator\AggregateHydrator as Hydrator;
use Company\Form\CompanyForm as Form;
use Savve\Filter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class NewFormFactory implements
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
        $object = new Entity();

        // filter and validation
        $input = $inputFilter->get('website');
        $filterChain = $input->getFilterChain();
        $validatorChain = $input->getValidatorChain();

        // remove http|https|ftp from the website string
        $filter = new Filter\Hostname();
        $filterChain->attach($filter, 10);

        // form
        $form = new Form('company-create');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set form name
        $form->setLabel('New Company');

        // form button
        $form->get('submit')
            ->setLabel('Create');

        // form validation
        $form->setValidationGroup([
            'name',
            'telephone',
            'fax',
            'street_address',
            'suburb',
            'postcode',
            'state',
            'country',
            'website',
            'abn'
        ]);

        return $form;
    }
}