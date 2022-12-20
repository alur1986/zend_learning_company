<?php

namespace LearningPlan\Factory\Form;

use \ArrayObject as Object;
use Learning\Hydrator\AggregateHydrator as Hydrator;
use LearningPlan\InputFilter\LearningPlanInputFilter as InputFilter;
use LearningPlan\Form\LearningPlanForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Savve\Stdlib\Exception;

class CreateFormFactory implements
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
        $form = new Form('create-learning-plan');
        $form->setLabel('Learning Plan');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // change the submit button label
        // !! its not set in the Form Class !! can't do this here !!
        //$form->get('submit')
        //    ->setLabel('Create Learning Plan');

        // form validation
        $form->setValidationGroup([
            'title',
            'description',
            'catalog_display',
            'catalog_image',
            'catalog_thumb',
            'status'
        ]);

        return $form;
    }
}