<?php

namespace Resource\Validator;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\AbstractValidator;

class Uploaded extends AbstractValidator
{
    use ServiceLocatorAwareTrait;
    const ERROR_INVALID = 'invalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::ERROR_INVALID => "THIS IS INVALID MESSAGE"
    ];

    /**
     * Validation failure messages variables
     *
     * @var array Error message template variables
     */
    protected $messageVariables = [];

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct (ServiceLocatorInterface $serviceLocator, $options = [])
    {
        $this->setServiceLocator($serviceLocator);
        parent::__construct($options);
    }

    public function isValid ($value, $context = null)
    {
        $this->setValue($value);

        $this->error(self::ERROR_INVALID, $value);
        return false;
    }
}