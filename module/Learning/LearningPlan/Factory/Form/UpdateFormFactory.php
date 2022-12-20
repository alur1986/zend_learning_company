<?php

namespace LearningPlan\Factory\Form;

use \ArrayObject as Object;
use Learning\Hydrator\AggregateHydrator as Hydrator;
use LearningPlan\InputFilter\LearningPlanInputFilter as InputFilter;
use LearningPlan\Form\LearningPlanForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Savve\Stdlib\Exception;

class UpdateFormFactory implements
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
        $form = new Form('update-learning-plan');
        $form->setLabel('Learning Plan');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation
        $form->setValidationGroup([
            'plan_id',
            'site_id',
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