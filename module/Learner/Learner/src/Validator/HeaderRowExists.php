<?php

namespace Learner\Validator;

use \Traversable;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Stdlib\ErrorHandler;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\AbstractValidator;

class HeaderRowExists extends AbstractValidator
{
    const NOT_READABLE = 'fileImageSizeNotReadable';
    const NOT_FOUND = 'headerRowNotFound';
    const MISSING_FIELD = 'missingFieldInHeader';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_READABLE => "File is not readable or does not exist",
        self::NOT_FOUND => 'Column row must contain the following column names: %fieldNames%.',
        self::MISSING_FIELD => 'Validator field name option was not set.'
    ];

    /**
     * Validation failure messages variables
     *
     * @var array Error message template variables
     */
    protected $messageVariables = [
        'fieldNames' => 'fieldNames'
    ];

    /**
     * Field names to validate
     * @var string
     */
    public $fieldNames = null;

    /**
     * Returns true if and only if the CSV file contains the required header row columns
     *
     * @param string|array $value
     * @param array $file File data from \Zend\File\Transfer\Transfer (optional)
     * @return boolean
     */
    public function isValid ($value, $file = null)
    {
        if (is_string($value) && is_array($file)) {
            // Legacy Zend\Transfer API support
            $filename = $file['name'];
            $file = $file['tmp_name'];
        }
        elseif (is_array($value)) {
            if (!isset($value['tmp_name']) || !isset($value['name'])) {
                throw new Exception\InvalidArgumentException('Value array must be in $_FILES format');
            }
            $file = $value['tmp_name'];
            $filename = $value['name'];
        }
        else {
            $file = $value;
            $filename = basename($file);
        }
        $this->setValue($filename);

        // Is file readable ?
        if (empty($file) || false === stream_resolve_include_path($file)) {
            $this->error(self::NOT_READABLE);
            return false;
        }

        // if fieldnames to be search set in the options?
        if (!($fieldNames = $this->getFieldNames())) {
            $this->error(self::MISSING_FIELD);
            return false;
        }

        ErrorHandler::start();
        $file = stream_resolve_include_path($file);
        $fhandle = fopen($file, 'rb');
        $contents = stream_get_contents($fhandle);
        $array = Stdlib\Csv::parseFromString($contents);
        ErrorHandler::stop();

        // assuming the first row is the column name row
        $header = current($array);

        // check if the header have the fieldnames required
        $fieldNames = is_array($fieldNames) ? $fieldNames : explode(',', $fieldNames);
        foreach ($fieldNames as $key => $value) {
            $fieldNames[$key] = trim($value);
        }
        if (!array_intersect($header, $fieldNames)) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        return true;
    }

    /**
     *
     * @return the $fieldNames
     */
    public function getFieldNames ()
    {
        return $this->fieldNames;
    }

    /**
     *
     * @param string|array $fieldNames
     */
    public function setFieldNames ($fieldNames)
    {
        if (is_array($fieldNames)) {
            $fieldNames = implode(",", $fieldNames);
        }

        $this->fieldNames = $fieldNames;
        return $this;
    }
}