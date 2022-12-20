<?php

namespace Authorization\Form\Level;

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
            if ($instance->has('level')) {
                $element = $instance->get('level');
            }
            if ($instance->has('level_id')) {
                $element = $instance->get('level_id');
            }

            // only do something with multicheckbox or select form elements
            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {
                $currentValueOptions = $element->getValueOptions();

                // get all the resources from the system
                $collection = $serviceManager->get('Authorization\Level\All');

                // populate the element with value options
                $valueOptions = [];
                foreach ($collection as $item) {
                    $valueOptions[] = [
                        'label' => $item['name'],
                        'value' => $item['id'],
                        'description' => $item['description']
                    ];
                }
                $element->setValueOptions($valueOptions);
            }
        }
    }
}