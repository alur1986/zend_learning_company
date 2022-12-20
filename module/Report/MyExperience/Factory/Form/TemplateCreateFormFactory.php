<?php

namespace Report\MyExperience\Factory\Form;

use \ArrayObject as Object;
use Report\MyExperience\InputFilter\TemplateInputFilter as InputFilter;
use Report\MyExperience\Hydrator\AggregateHydrator as Hydrator;
use Report\MyExperience\Form\TemplateForm as Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class TemplateCreateFormFactory implements
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
        $form = new Form('report-create-tincan');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);

        // form validation group
        $form->setValidationGroup([
            'title',
            'description',
            'config'
        ]);

        $serviceManager = $serviceLocator->getServiceLocator();

        /* @var $options \Report\Service\Options */
        $options = $serviceManager->get('Report\Options');
        $columns = $options['myexperience']['available_template_columns'];
        $valueOptions = [];
        foreach ($columns as $column) {
            $valueOptions[] = [
                'label' => $column['title'],
                'value' => $column['key']
            ];
        }
        $form->get('available_columns')
            ->setValueOptions($valueOptions);

        // set default value
        $form->get('title')
            ->setValue('MyExperience Report Template - ' . time());

        return $form;
    }
}