<?php

namespace Report\LearningProgressSummary\Factory\Form;

use \ArrayObject as Object;
use Report\LearningProgressSummary\InputFilter\FilterInputFilter as InputFilter;
use Report\LearningProgressSummary\Hydrator\AggregateHydrator as Hydrator;
use Report\LearningProgressSummary\Form\FilterForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class FilterRangeFormFactory implements
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
        $form = new Form('report-learning-progress-summary-filter');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'filter_id',
            'show_from',
            'show_to',
            'all_dates',
            'learner_status'
        ]);

        return $form;
    }
}