<?php

namespace Notification\Factory\Form;

use \ArrayObject as Object;
use Notification\Form\GroupForm as Form;
use Notification\Hydrator\AggregateHydrator as Hydrator;
use Notification\InputFilter\Notification as InputFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class GroupFormFactory implements
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

        // instantiate form
        $form = new Form('group-notification');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation
        $form->setValidationGroup([
            'notification_id',
            'group_id'
        ]);

        return $form;
    }
}