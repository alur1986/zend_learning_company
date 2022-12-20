<?php

namespace Report\MyLocker\Form;

use Savve\Form\AbstractForm;

class FilterCategoriesForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name = null, $options = [])
    {
        parent::__construct($name, $options);

        // filter_id : hidden
        $this->add([
            'name' => 'filter_id',
            'type' => 'Hidden',
            'required' => true
        ]);

        // category_id : multicheckbox
        $this->add([
            'name' => 'category_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Select categories',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
                'value_options' => []
            ]
        ]);
    }
}