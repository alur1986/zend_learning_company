<?php

namespace Agent\Form;

use Savve\Stdlib;
use Zend\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class Initializer implements
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
            if ($instance->has('company_id')) {
                $element = $instance->get('company_id');
            }
            elseif ($instance->has('company_ids')) {
                $element = $instance->get('company_ids');
            }

            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {

                // get the current value options
                $current = $element->getValueOptions();

                /* @var $collection \Doctrine\Common\Collections\ArrayCollection */
                $collection = $serviceManager->get('Company\All');
                $valueOptions = [];
                foreach ($collection as $item) {
                    $valueOptions[] = array_merge([
                        'label' => $item['name'],
                        'value' => $item['company_id']
                    ], Stdlib\ObjectUtils::toArray($item));
                }
                $element->setValueOptions($valueOptions);
            }
        }
    }
}