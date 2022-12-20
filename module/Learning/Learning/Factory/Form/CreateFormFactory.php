<?php

namespace Learning\Factory\Form;

use \ArrayObject as Object;
use Learning\Hydrator\AggregateHydrator as Hydrator;
use Learning\InputFilter\LearningInputFilter as InputFilter;
use Learning\Form\LearningForm as Form;
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
        $form = new Form('create-learning-activity');
        $form->setLabel('Learning Activity');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // change the submit button label
        $form->get('submit')
            ->setLabel('Create Learning');

        // form validation
        $form->setValidationGroup([
            'activity_type',
            'title',
            'description',
            'keyword',
            'prerequisites',
            'prerequisite',
            'learning_objective',
            'catalog_description',
            'catalog_display',
            'catalog_thumb',
            'catalog_image',
            'code',
            'version',
            'cpd',
            'auto_approve',
            'needs_enrolment',
            'duration',
            'direct_cost',
            'indirect_cost',
            'status',
            'category_id',
            'auto_distribute',
            'auto_distribute_on_registration',
            'auto_distribute_on_login',
            'ordering',
            'auto_distribute_delay',
            'plan_id'
        ]);

        return $form;
    }
}