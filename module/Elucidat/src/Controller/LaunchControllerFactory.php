<?php

namespace Elucidat\Controller;

use Elucidat\Controller\LaunchController as Controller;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Mvc\Controller\ControllerManager;

class LaunchControllerFactory
{

    /**
     * Create the service
     *
     * @param ServiceLocatorAwareInterface $controllerManager
     * @return \Elucidat\Controller\LaunchController
     */
    public function __invoke (ServiceLocatorAwareInterface $controllerManager)
    {
        $serviceManager = $controllerManager->getServiceLocator();

        // @todo instantiate dependencies here
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        // instantiate controller and inject dependencies
        $controller = new Controller($entityManager);
        return $controller;
    }
}