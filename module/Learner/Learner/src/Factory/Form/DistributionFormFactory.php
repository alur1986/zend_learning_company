<?php

namespace Learner\Factory\Form;

use \ArrayObject as Object;
//use Distribution\Learning\Hydrator\AggregateHydrator as Hydrator;
use Learner\Hydrator\AggregateHydrator as Hydrator;
use Learner\InputFilter\DistributionFilter as InputFilter;
use Learner\Form\DistributionForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DistributionFormFactory implements
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
        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // form
        $form = new Form('distribution-learner');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'user_id',
            'activity_id',
            'distribution_date',
            'expiry_date'
        ]);

        return $form;
    }
}