<?php

namespace Report\Interactions\Form;

use Savve\Form\AbstractForm;

class IndexForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, array $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', 'report-interactions');
        $this->setAttribute('name', 'report-interactions');

        // generate_from : date
        $currentDate = new \DateTime();
        $this->add([
                       'name' => 'generate_report_at',
                       'type' => 'MonthSelect',
                       'options' => [
                           'label' => 'Select a month and a year to generate report',
                           'min_year' => intval($currentDate->format('Y')) - 1
                       ],
                       'attributes' => [
                           'value' => $currentDate
                       ]
                   ]);
    }
}

