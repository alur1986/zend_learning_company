<?php

namespace Report\LearningPlaylist\Factory\Form;

use \ArrayObject as Object;
use Report\LearningPlaylist\InputFilter\InputFilter as InputFilter;
use Report\LearningPlaylist\Hydrator\AggregateHydrator as Hydrator;
use Report\LearningPlaylist\Form\LearnersForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearnersFormFactory implements
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
        $form = new Form('report-learning-playlist');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'learner_id'
        ]);

        return $form;
    }
}