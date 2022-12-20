<?php

namespace Learning\Taxonomy\Form;

use Taxonomy\Form\TaxonomyForm as AbstractForm;

class TaxonomyForm extends AbstractForm
{

    /**
     * Constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct ($name, $options = [])
    {
        parent::__construct($name, $options);
    }
}