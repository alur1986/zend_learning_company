<?php

namespace Scorm12\Factory\Service;

use Scorm12\Service\OptionsService as Service;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class OptionsServiceFactory implements
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
        $config = $this->getConfig($serviceLocator);
        if (!$config) {
            return new Service();
        }
        $service = new Service($config);


        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();

        if ($routeMatch && ($activityId = $routeMatch->getParam('activity_id'))) {
         	$courseFilePath = $service->getCourseFilePath();
         	$courseFilePath = Stdlib\StringUtils::sprintf($courseFilePath, [ 'activityId' => $activityId ]);
         	$service->setCourseFilePath($courseFilePath);
        }

        return $service;
    }

    /**
     * Get the module optional config array from the Configuration service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    public function getConfig (ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!$config) {
            return [];
        }

        if (!isset($config['learning_options']) || empty($config['learning_options'])) {
            return [];
        }

        $config = $config['learning_options'];
        if (!isset($config['scorm12']) || empty($config['scorm12'])) {
            return [];
        }

        $config = $config['scorm12'];
        return $config;
    }
}