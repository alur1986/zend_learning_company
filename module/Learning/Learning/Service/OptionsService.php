<?php

namespace Learning\Service;

use Savve\Stdlib\AbstractOptions;

class OptionsService extends AbstractOptions
{

    /**
     * Learning Activity Types
     *
     * @var array
     */
    protected $activityTypes;

    /**
     * Learning Types
     *
     * @var array
     */
    protected $learningTypes;

    /**
     * Event types
     *
     * @var array
     */
    protected $eventTypes;

    /**
     * Assessment Types
     *
     * @var array
     */
    protected $assessmentTypes;

    /**
     * File Upload Path
     *
     * @var string
     */
    protected $fileUploadPath;

    /**
     * File base URI
     *
     * @var string
     */
    protected $fileBaseUri;

    /**
     *
     */
    protected $oldFileBaseUri;
    /**
     * Backwards-compatible of the old course path
     *
     * @var string
     */
    protected $oldCourseFilePath;

    /**
     *
     * @return array $activityTypes
     */
    public function getActivityTypes ()
    {
        return $this->activityTypes;
    }

    /**
     * @param mixed $oldFileBaseUri
     *
     * @return $this
     */
    public function setOldFileBaseUri( $oldFileBaseUri )
    {
        $this->oldFileBaseUri = $oldFileBaseUri;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldFileBaseUri()
    {
        return $this->oldFileBaseUri;
    }

    /**
     *
     * @param array $activityTypes
     * @return OptionsService
     */
    public function setActivityTypes ($activityTypes)
    {
        $this->activityTypes = $activityTypes;
        return $this;
    }

    /**
     *
     * @return array $learningTypes
     */
    public function getLearningTypes ()
    {
        return $this->learningTypes;
    }

    /**
     *
     * @param array $learningTypes
     * @return OptionsService
     */
    public function setLearningTypes ($learningTypes)
    {
        $this->learningTypes = $learningTypes;
        return $this;
    }

    /**
     *
     * @return array $eventTypes
     */
    public function getEventTypes ()
    {
        return $this->eventTypes;
    }

    /**
     *
     * @param array $eventTypes
     * @return OptionsService
     */
    public function setEventTypes ($eventTypes)
    {
        $this->eventTypes = $eventTypes;
        return $this;
    }

    /**
     *
     * @return array $assessmentTypes
     */
    public function getAssessmentTypes ()
    {
        return $this->assessmentTypes;
    }

    /**
     *
     * @param array $assessmentTypes
     * @return OptionsService
     */
    public function setAssessmentTypes ($assessmentTypes)
    {
        $this->assessmentTypes = $assessmentTypes;
        return $this;
    }

    /**
     *
     * @return string $fileUploadPath
     */
    public function getFileUploadPath ()
    {
        return $this->fileUploadPath;
    }

    /**
     *
     * @param string $fileUploadPath
     * @return OptionsService
     */
    public function setFileUploadPath ($fileUploadPath)
    {
        $this->fileUploadPath = $fileUploadPath;
        return $this;
    }

    /**
     *
     * @return string $fileBaseUri
     */
    public function getFileBaseUri ()
    {
        return $this->fileBaseUri;
    }

    /**
     *
     * @param string $fileBaseUri
     * @return OptionsService
     */
    public function setFileBaseUri ($fileBaseUri)
    {
        $this->fileBaseUri = $fileBaseUri;
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