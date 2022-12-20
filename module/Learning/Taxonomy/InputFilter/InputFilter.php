<?php

namespace Learning\Taxonomy\InputFilter;

use Taxonomy\InputFilter\InputFilter as AbstractInputFilter;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\Between;
use Zend\InputFilter\Factory;

class InputFilter extends AbstractInputFilter
{

    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
    }
}