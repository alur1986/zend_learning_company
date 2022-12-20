<?php

namespace Scorm12\Service;

use Savve\Stdlib\AbstractOptions;

class OptionsService extends AbstractOptions
{

    /**
     * Scorm 1.2 decompressed course file path
     *
     * @var string
     */
    protected $courseFilePath;

    /**
     * Scorm 1.2 manifest filename
     *
     * @var string
     */
    protected $manifestFilename = 'imsmanifest.xml';

    /**
     * Backwards-compatible of the old course path
     *
     * @var string
     */
    protected $oldCourseFilePath;

    /**
     * Base uri of the course
     */
    protected $baseUri;
    /**
     *
     * @return string $courseFilePath
     */
    public function getCourseFilePath ()
    {
        return $this->courseFilePath;
    }

    /**
     *
     * @param string $courseFilePath
     * @return OptionsService
     */
    public function setCourseFilePath ($courseFilePath)
    {
        $this->courseFilePath = $courseFilePath;
        return $this;
    }

    /**
     *
     * @return string $manifestFilename
     */
    public function getManifestFilename ()
    {
        return $this->manifestFilename;
    }

    /**
     *
     * @param string $manifestFilename
     * @return OptionsService
     */
    public function setManifestFilename ($manifestFilename)
    {
        $this->manifestFilename = $manifestFilename;
        return $this;
    }

    /**
     *
     * @return string $oldCourseFilePath
     */
    public function getOldCourseFilePath ()
    {
        return $this->oldCourseFilePath;
    }

    /**
     *
     * @param string $oldCourseFilePath
     * @return OptionsService
     */
    public function setOldCourseFilePath ($oldCourseFilePath)
    {
        $this->oldCourseFilePath = $oldCourseFilePath;
        return $this;
    }
}