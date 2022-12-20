<?php

namespace Authorization\Factory\Form\Rule;

use \ArrayObject as Object;
use Authorization\Hydrator\Rule\AggregateHydrator as Hydrator;
use Authorization\InputFilter\Rule\InputFilter;
use Authorization\Form\Rule\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CreateFormFactory implements
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
        $form = new Form('authorization-rule-create');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        $serviceManager = $serviceLocator->getServiceLocator();
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if ($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            /* @var $service \Authorization\Service\AuthorizationService */
            $service = $serviceManager->get('Zend\Authorization\AuthorizationService');
            $roleId = $routeMatch->getParam('role_id');
            $role = $roleId ? $service->findOneRoleById($roleId) : null;
            if ($role) {
                // modify the role_id element
                $form->remove('role_id');
                $form->add([
                    'name' => 'role_id',
                    'type' => 'Hidden',
                    'attributes' => [
                        'value' => $roleId
                    ]
                ]);
            }
        }

        // validation
        $form->setValidationGroup([
            'site_id',
            'role_id',
            'resource_id',
            'permission'
        ]);

        return $form;
    }
}