<?php

namespace Learning\Form;

use Savve\Stdlib;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class Initialiser implements
        InitializerInterface
{

    /**
     * Initialize services
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize ($instance, ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        if ($instance instanceof Form\Fieldset) {
            $element = null;
            if ($instance->has('activity_id')) {
                $element = $instance->get('activity_id');
            }
            elseif ($instance->has('activity_ids')) {
                $element = $instance->get('activity_ids');
            }

            // add value options to Select form elements that requires it
            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {
                $valueOptions = $serviceManager->get('Learning\Activities');
                $element->setValueOptions($valueOptions);
            }
        }
    }
}