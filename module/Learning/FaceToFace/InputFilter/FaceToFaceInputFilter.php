<?php

namespace FaceToFace\InputFilter;

use Learning\InputFilter\LearningInputFilter as AbstractInputFilter;
use Zend\I18n\Validator\Alnum;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\InputFilter\Factory;
use Savve\Filter\AlphaNumeric;
use Zend\Validator\Regex;
use Zend\Validator\Between;

class FaceToFaceInputFilter extends AbstractInputFilter
{
}