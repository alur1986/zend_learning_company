<?php

namespace WrittenAssessment\Factory\Form;

use \ArrayObject as Object;
use WrittenAssessment\InputFilter\WrittenAssessmentInputFilter as InputFilter;
use WrittenAssessment\Hydrator\AggregateHydrator as Hydrator;
use WrittenAssessment\Form\WrittenAssessmentForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class UpdateActivityFormFactory implements
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
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // form
        $form = new Form('update-written-assessment');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // process form post
        /* @var $post \Zend\Stdlib\Parameters */
        $post = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRequest()
            ->getPost()
            ->toArray();

        if (!empty($post)) {
            if (isset($post['auto_distribute']) && ($post['auto_distribute'] == 'on' || $post['auto_distribute'] == true)) {
                if (($post['auto_distribute_on_registration'] == 0 || $post['auto_distribute_on_registration'] == false) && ($post['auto_distribute_on_login'] == 0 || $post['auto_distribute_on_login'] == false)) {
                    /* we really need to also 'empty' the value (or set it to null) but cant do that here, need to test this in the controller as well */
                    $inputFilter->get('auto_distribute')->setRequired(true);
                }
            }
        }

        // populate element values
        $form->get('activity_type')
            ->setValue('written-assessment');

        // form validation group
        $form->setValidationGroup([
            'activity_id',
            'activity_type',
            'title',
            'description',
            'keywords',
            'prerequisites',
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
            'plan_id',
            'auto_distribute',
            'ordering',
            'auto_distribute_on_registration',
            'auto_distribute_on_login',
            'auto_distribute_delay'
        ]);

        return $form;
    }
}