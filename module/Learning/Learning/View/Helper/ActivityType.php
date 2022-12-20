<?php

namespace Learning\View\Helper;

use Savve\Stdlib\Exception;
use Savve\View\Helper\AbstractViewHelper;
use Zend\Config\Config;

class ActivityType extends AbstractViewHelper
{

    /**
     * Collection of available learning activity types
     *
     * @var Config
     */
    protected $activityTypes = [];

    /**
     * Current activity type data
     *
     * @var array
     */
    protected $activityType = [];

    /**
     * Constructor
     *
     * @param array|\Zend\Config\Config $activityTypes
     */
    public function __construct ($activityTypes)
    {
        $this->activityTypes = $activityTypes;
    }

    /**
     *
     * @param $activityType
     *
     * @return $this
     */
    public function __invoke ($activityType)
    {
        $activityTypes = $this->activityTypes;
        if (isset($activityTypes[$activityType]) && $activityTypes[$activityType]) {
            $this->activityType = $activityTypes[$activityType];
            return $this;
        }
        throw new Exception\InvalidArgumentException(sprintf('Activity Type does not exist. Check your configuration for the current activity type.'));
    }

    /**
     * Return the activity type key
     *
     * @return string
     */
    public function activityType ()
    {
        return isset($this->activityType['type']) && $this->activityType['type'] ? $this->activityType['type'] : null;
    }

    /**
     * Alias of self::activityType()
     *
     * @return string
     */
    public function type ()
    {
        return $this->activityType();
    }

    /**
     * Return the activity type title
     *
     * @return string
     */
    public function title ()
    {
        return isset($this->activityType['title']) && $this->activityType['title'] ? $this->activityType['title'] : null;
    }

    /**
     * Method overloading to display the activity title or type when the object is treated as a string
     *
     * @return string
     */
    public function toString ()
    {
        $activityType = $this->title() ?  : $this->activityType();
        $translator = $this->getTranslator();
        return $translator->translate($activityType);
    }
}