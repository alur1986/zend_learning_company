<?php

namespace Agent\Factory\Form;

use Savvecentral\Entity\Agent as Entity;
use Agent\InputFilter\Agent as InputFilter;
use Agent\Hydrator\AggregateHydrator as Hydrator;
use Agent\Form\AgentForm as Form;
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
     //   $input = $inputFilter->get('website');
     //   $filterChain = $input->getFilterChain();
     //   $validatorChain = $input->getValidatorChain();

    //    // remove http|https|ftp from the website string
    //    $filter = new Filter\Hostname();
    //    $filterChain->attach($filter, 10);

        // form
        $form = new Form('agent-create');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // set form name
        $form->setLabel('New Agent / Agency');

        // form button
        $form->get('submit')
            ->setLabel('Create');

        // form validation
        $form->setValidationGroup([
            'name',
            'code',
            'password'
        ]);

        return $form;
    }
}