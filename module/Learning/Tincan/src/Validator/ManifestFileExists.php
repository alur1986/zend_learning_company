<?php

namespace Tincan\Validator;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use \Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\AbstractValidator;

class ManifestFileExists extends AbstractValidator
{
    const FILE_NOT_EXISTS = 'fileNotExists';
    const FILE_NOT_ACCESSIBLE = 'fileNotAccessible';
    const MANIFEST_NOT_EXISTS = 'manifestFileNotExists';
    const PLUGIN_NOT_INSTALLED = 'ZipArchiveNotInstalled';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::FILE_NOT_EXISTS => 'Tincan ZIP file does not exists',
        self::MANIFEST_NOT_EXISTS => 'Tincan manifest file does not exists',
        self::PLUGIN_NOT_INSTALLED => 'ZipArchive PHP module is not installed in this server'
    ];

    /**
     * Manifest file name
     *
     * @var string
     */
    private $manifestFileName = 'tincan.xml';

    /**
     * Validate
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
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

        // set the value to be validated
        $this->setValue($filename);

        // is file readable?
        if (empty($file) || false === stream_resolve_include_path($file)) {
            $this->error(self::FILE_NOT_EXISTS);
            return false;
        }

        // check if the ZipArchive PHP plugin module is installed
        if (!class_exists('ZipArchive')) {
            $this->error(self::PLUGIN_NOT_INSTALLED);
            return false;
        }

        // this module must be installed as part of PHP to work.
        $zip = new \ZipArchive();
        if ($zip->open($file) === false) {
            $this->error(self::FILE_NOT_ACCESSIBLE);
            return false;
        }

        // check if the ZIP file contains the manifest file
        if ($zip->locateName($this->manifestFileName) === false) {
            // if the archive files are contained within a parent directory this first test will fail
            $found = false;
            for( $i = 0; $i < $zip->numFiles; $i++ ){
                $stat = $zip->statIndex( $i );
                if (strpos($stat['name'], $this->manifestFileName) !== false) {
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                $this->error(self::MANIFEST_NOT_EXISTS);
                return false;
            }
        }
        $zip->close();

        return true;
    }
}