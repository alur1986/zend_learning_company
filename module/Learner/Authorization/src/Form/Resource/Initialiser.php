<?php

namespace Authorization\Form\Resource;

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
            if ($instance->has('resource')) {
                $element = $instance->get('resource');
            }
            if ($instance->has('resources')) {
                $element = $instance->get('resources');
            }
            if ($instance->has('resource_id')) {
                $element = $instance->get('resource_id');
            }

            // only do something with multicheckbox or select form elements
            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {
                $currentValueOptions = $element->getValueOptions();

                // get all the resources from the system
                $collection = $serviceManager->get('Authorization\Resources\All');

                // populate the element with value options
                $valueOptions = [];
                foreach ($collection as $item) {
                    $valueOptions[] = [
                        'label' => $item['title'],
                        'value' => $item['id'],
                        'description' => $item['description'],
                        'type' => $item['type'],
                        'resource' => $item['resource']
                    ];
                }
                $element->setValueOptions($valueOptions);
            }
        }
    }
}