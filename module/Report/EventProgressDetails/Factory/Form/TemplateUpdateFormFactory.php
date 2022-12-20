<?php

namespace Report\EventProgressDetails\Factory\Form;

use \ArrayObject as Object;
use Report\EventProgressDetails\InputFilter\TemplateInputFilter as InputFilter;
use Report\EventProgressDetails\Hydrator\AggregateHydrator as Hydrator;
use Report\EventProgressDetails\Form\TemplateForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class TemplateUpdateFormFactory implements
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
        $form = new Form('report-event-progress-details-template');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'template_id',
            'title',
            'description',
            'available_columns',
            'config'
        ]);

        $serviceManager = $serviceLocator->getServiceLocator();

        /* @var $options \Report\Service\Options */
        $options = $serviceManager->get('Report\Options');
        $columns = $options['event_progress_summary']['available_template_columns'];
        $valueOptions = [];
        foreach ($columns as $column) {
            $valueOptions[] = [
                'label' => $column['title'],
                'value' => $column['key']
            ];
        }
        $form->get('available_columns')
            ->setValueOptions($valueOptions);

        return $form;
    }
}