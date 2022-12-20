<?php

namespace Report\Interactions\InputFilter;

use Zend\InputFilter\InputFilter as AbstractInputFilter;

class InputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        $this->add([
            'name' => 'generate_report_at',
            'required' => false,
            'filters' => [
                [
                    'name' => 'MonthSelect'
                ]
            ],
            'validators' => [
                [
                    'name' => 'Callback',
                    'options' => [
                        'callback' => function  ($value, $context = [])
                        {
                            $currentDate = new \DateTime();
                            $currentYear = $currentDate->format('Y');
                            $currentMonth = $currentDate->format('m');
                            list($selectedYear, $selectedMonth) = explode('-', $value);
                            return intVal($selectedYear) <= intval($currentYear) &&
                                   intVal($selectedYear) >= (intval($currentYear) - 1) &&
                                   intVal($selectedMonth) >= 1 && intVal($selectedMonth) <= 12 &&
                                   intVal($selectedMonth) <= intval($currentMonth);
                        },
                        'message' => "The selected value should not exceed than current month/year"
                    ]
                ]
            ]
        ]);

    }
}
