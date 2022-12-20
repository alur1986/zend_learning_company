<?php

namespace Report\MyLocker\Form;

use Savve\Form\AbstractForm;

class CategoriesForm extends AbstractForm
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

        // category_id
        $this->add([
            'name' => 'category_id',
            'type' => 'MultiCheckbox',
            'options' => [
                'label' => 'Category',
                'empty_option' => 'Uncategorised',
                'label_options' => [
                    'disable_html_escape' => true
                ],
                'value_options' => []
            ],
            'label_options' => [
                'disable_html_escape' => true
            ]
        ]);
    }
}