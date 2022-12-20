<?php

namespace OnTheJobAssessment\Factory\Form;

use \ArrayObject as Object;
use OnTheJobAssessment\InputFilter\OnTheJobAssessmentInputFilter as InputFilter;
use OnTheJobAssessment\Hydrator\AggregateHydrator as Hydrator;
use OnTheJobAssessment\Form\OnTheJobAssessmentForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateOnTheJobAssessmentFormFactory implements
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

        // form
        $form = new Form('create-on-the-job-assessment');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'site_id',
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

        // populate element values
        $form->get('activity_type')
            ->setValue('on-the-job-assessment');

        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');
        $form->get('site_id')
            ->setValue($siteId);

        return $form;
    }
}