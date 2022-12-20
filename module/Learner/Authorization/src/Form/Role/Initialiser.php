<?php

namespace Authorization\Form\Role;

use Savve\Stdlib;
use Zend\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class Initialiser implements
        InitializerInterface
{

    /**
     * Initialize
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
            if ($instance->has('parent')) {
                $element = $instance->get('parent');
            }
            if ($instance->has('role')) {
                $element = $instance->get('role');
            }
            if ($instance->has('roles')) {
                $element = $instance->get('roles');
            }
            if ($instance->has('role_id')) {
                $element = $instance->get('role_id');
            }

            // only do something with multicheckbox or select form elements
            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {
                $currentValueOptions = $element->getValueOptions();

                // get all the roles from the system
                $collection = $serviceManager->get('Authorization\Roles\All');

                // populate the element with value options
                $valueOptions = [];
                foreach ($collection as $item) {
                    $valueOptions[] = ['label' => $item['title'], 'value' => $item['id']];
                }
                $element->setValueOptions($valueOptions);
            }
        }
    }
}