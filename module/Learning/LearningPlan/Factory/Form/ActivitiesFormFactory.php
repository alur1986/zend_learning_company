<?php

namespace LearningPlan\Factory\Form;

use \ArrayObject as Object;
use Learning\Hydrator\AggregateHydrator as Hydrator;
use LearningPlan\InputFilter\LearningPlanInputFilter as InputFilter;
use LearningPlan\Form\ActivitiesForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Savve\Stdlib\Exception;

class ActivitiesFormFactory implements
    FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // instantiate form
        $form = new Form('add-learning-plan-activities');
        $form->setLabel('Learning Plan Activities');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // change the submit button label
        // !! its not set in the Form Class !! can't do this here !!
        //$form->get('submit')
        //    ->setLabel('Create Learning Plan');

        // form validation
        $form->setValidationGroup([
            'plan_id',
            'site_id',
            'config',
            'confirm_ordering'
        ]);

        return $form;
    }
}