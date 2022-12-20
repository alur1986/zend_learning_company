<?php

namespace Learner\Form;

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
     *
     * @return mixed
     */
    public function initialize ($instance, ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        // get the route params
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch) {
            return;
        }
        $siteId = $routeMatch->getParam('site_id');

        if ($instance instanceof Form\Fieldset) {
            $element = null;
            if ($instance->has('user_id')) {
                $element = $instance->get('user_id');
            }
            elseif ($instance->has('learner_id')) {
                $element = $instance->get('learner_id');
            }
            elseif ($instance->has('user_ids')) {
                $element = $instance->get('user_ids');
            }
            elseif ($instance->has('learner_ids')) {
                $element = $instance->get('learner_ids');
            }

            if ($element instanceof Form\Element\MultiCheckbox || $element instanceof Form\Element\Select) {

                // get the current value options
                $current = $element->getValueOptions();
                if (!$element->hasAttribute('block-load')) {
                    // get all the learners
                    $learners = $serviceManager->get('Learner\All');
                    $valueOptions = [];
                    foreach ($learners as $learner) {
                        $array = [
                            'label' => $learner['name'],
                            'value' => $learner['user_id'],
                            'user_id' => $learner['user_id'],
                            'first_name' => $learner['first_name'],
                            'last_name' => $learner['last_name'],
                            'name' => $learner['name'],
                            'email' => $learner['email'],
                            'telephone' => isset($learner['telephone']) ? $learner['telephone'] : null,
                            'mobile_number' => isset($learner['mobile_number']) ? $learner['mobile_number'] : null,
                            'address' => isset($learner['address']) ? $learner['address'] : null,
                            'status' => $learner['status'],
                            'profile_picture_uri' => isset($learner['profile_picture_uri']) ? $learner['profile_picture_uri'] : null,
                            'profile_picture' => isset($learner['profile_picture']) ? $learner['profile_picture'] : null,
                            'role' => isset($learner['role']) ? $learner['role'] : null,
                            'employment_id' => isset($learner['employment_id']) ? $learner['employment_id'] : null,
                            'site' => isset($learner['site']) ? $learner['site'] : null
                        ];

                        $valueOptions[] = $array;
                    }

                    $element->setValueOptions($valueOptions);
                }
            }
        }
    }
}