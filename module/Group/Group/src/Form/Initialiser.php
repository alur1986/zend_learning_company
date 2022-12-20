<?php

namespace Group\Form;

use Savve\Stdlib;
use Zend\Form;
use Zend\Form\Element;
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

        // get the route params
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch) {
            return;
        }

        $siteId = $routeMatch->getParam('site_id');
        $groupId = $routeMatch->getParam('group_id');

        if ($instance instanceof Form\Fieldset) {

            if ($instance->has('group_id') || $instance->has('group_ids') || $instance->has('groups') || $instance->has('group')) {
                $element = null;
                if ($instance->has('group_ids')) {
                    $element = $instance->get('group_ids');
                }
                elseif ($instance->has('group_id')) {
                    $element = $instance->get('group_id');
                }
                elseif ($instance->has('groups')) {
                    $element = $instance->get('groups');
                }
                elseif ($instance->has('group')) {
                    $element = $instance->get('group');
                }

                if ($element instanceof Element\Select || $element instanceof Element\MultiCheckbox) {
                    // populate select element with groups in key/value pairs
                    $groups = $serviceManager->get('Group\ActiveGroups');

                    // create a key/value pairs for valueOptions
                    $data = [];
                    foreach ($groups as $group) {
                        $item = [];
                        $item['label'] = $group->getName();
                        $item['value'] = $group->getGroupId();
                        $item = Stdlib\ArrayUtils::merge($item, $group->toArray());
                        $data[] = $item;
                    }

                    $element->setValueOptions($data);
                }
                elseif ($element instanceof Element\Hidden || $element instanceof Element\Text) {
                    // set default group_id value
                    $element->setValue($groupId);
                }
            }
        }
    }
}