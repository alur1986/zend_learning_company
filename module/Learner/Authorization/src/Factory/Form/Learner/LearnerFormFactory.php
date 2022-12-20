<?php

namespace Authorization\Factory\Form\Learner;

use \ArrayObject as Object;
use Authorization\Hydrator\Learner\AggregateHydrator as Hydrator;
use Authorization\InputFilter\Learner\InputFilter;
use Authorization\Form\Learner\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnerFormFactory implements
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
        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        // form
        $form = new Form('authorization-learner-add');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        $serviceManager = $serviceLocator->getServiceLocator();
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if ($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            $roleId = $routeMatch->getParam('role_id');
            if ($roleId && $form->has('role_id')) {
                $element = $form->get('role_id');
                $element->setValue($roleId);
            }
        }

        // validation
        $form->setValidationGroup([
            'role_id',
            'learner_id'
        ]);

        return $form;
    }
}