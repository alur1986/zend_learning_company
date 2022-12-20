<?php

namespace Notification\Factory\Form;

use \ArrayObject as Object;
use Notification\Form\NotificationForm as Form;
use Notification\Hydrator\AggregateHydrator as Hydrator;
use Notification\InputFilter\Notification as InputFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class UpdateFormFactory implements
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
        $form = new Form('learner');
        $form->setLabel('Edit Learner');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation
        $form->setValidationGroup([
            'notification_id',
            'subject',
            'message',
            'sender_name',
            'sender_email',
            'active_from',
            'active_to',
            'learner_id',
            'group_id',
            'send_to_all'
        ]);

        return $form;
    }
}