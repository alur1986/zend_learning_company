<?php

namespace Scorm12\Factory\Form;

use \ArrayObject as Object;
use Scorm12\InputFilter\Scorm12InputFilter as InputFilter;
use Scorm12\Hydrator\AggregateHydrator as Hydrator;
use Scorm12\Form\ActivityForm as Form;
use Scorm12\Form\Fieldset;
use Savvecentral\Entity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateScorm12FormFactory implements
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
        $form = new Form('create-scorm12');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // add the launch setting fieldset
        $fieldset = new Fieldset\LaunchFieldset('scorm12_activity');
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Entity\Scorm12Activity());
        $form->add($fieldset, [ 'name' => 'scorm12_activity' ]);

        // add input filter for the launch setting fieldset
        $scorm12ActivityInputFilter = new \Scorm12\InputFilter\LaunchInputFilter();
        $inputFilter = $form->getInputFilter();
        $inputFilter->add($scorm12ActivityInputFilter, 'scorm12_activity');

        // form validation group
        $form->setValidationGroup([
            'site_id',
            'activity_type',
            'title',
            'description',
            'keywords',
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
            'plan_id',
            'ordering',
            'auto_distribute',
            'auto_distribute_on_registration',
            'auto_distribute_on_login',
            'auto_distribute_delay',
            'licensed',
            'scorm12_activity' => [
                'allowed_attempts',
                'allow_review_on_completion',
                'allow_review_on_fail',
                'allow_tracking_override_after_completion',
                'window_scrollable',
                'window_width',
                'window_height'
            ]
        ]);

        // populate element values
        $form->get('activity_type')
            ->setValue('scorm12');

        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');
        $form->get('site_id')
            ->setValue($siteId);

        return $form;
    }
}