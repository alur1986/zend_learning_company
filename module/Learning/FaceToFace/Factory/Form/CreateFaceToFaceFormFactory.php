<?php

namespace FaceToFace\Factory\Form;

use \ArrayObject as Object;
use FaceToFace\InputFilter\FaceToFaceInputFilter as InputFilter;
use FaceToFace\Hydrator\AggregateHydrator as Hydrator;
use FaceToFace\Form\FaceToFaceForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateFaceToFaceFormFactory implements
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

        /// process form post
        /* @var $post \Zend\Stdlib\Parameters */
        $post = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRequest()
            ->getPost()
            ->toArray();

        if (!empty($post)) {
            if (isset($post['auto_distribute']) && ($post['auto_distribute'] == 'on' || $post['auto_distribute'] == true)) {
                if (($post['auto_distribute_on_registration'] == 0 || $post['auto_distribute_on_registration'] == false) && ($post['auto_distribute_on_login'] == 0 || $post['auto_distribute_on_login'] == false)) {
                    /* we really need to also 'empty' the valu (or set it to null) but cant do thast here, need to test this in the controller as well */
                    $inputFilter->get('auto_distribute')->setRequired(true);
                }
            }
        }

        // form
        $form = new Form('create-face-to-face');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'site_id',
            'category_id',
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
            'plan_id',
            'auto_distribute',
            'ordering',
            'auto_distribute_on_registration',
            'auto_distribute_on_login',
            'auto_distribute_delay'
        ]);

        // populate element values
        $form->get('activity_type')
            ->setValue('face-to-face');

        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $siteId = $routeMatch->getParam('site_id');
        $form->get('site_id')
            ->setValue($siteId);

        return $form;
    }
}