<?php

namespace Learner\Service;

use Savve\Stdlib\AbstractOptions;

class Options extends AbstractOptions
{

    /**
     * Password token expiry length
     *
     * @var int
     */
    protected $passwordTokenExpiry;

    /**
     * File upload path
     *
     * @var string
     */
    protected $uploadPath;

    /**
     * Base URI of upload files
     *
     * @var string
     */
    protected $baseUri;

    /**
     * Get the learner settings options
     *
     * @var array
     */
    protected $settings;

    /**
     * Profile photo placeholder image file path or URL
     *
     * @var string
     */
    protected $profilePhotoPlaceholder;

    /**
     * Get the password expiry length
     *
     * @return int
     */
    public function getPasswordTokenExpiry ()
    {
        return $this->passwordTokenExpiry ?  : 60 * 60 * 24;
    }

    /**
     * Set the password token expiry length
     *
     * @param int $passwordTokenExpiry
     * @return Options
     */
    public function setPasswordTokenExpiry ($passwordTokenExpiry)
    {
        $this->passwordTokenExpiry = $passwordTokenExpiry;
        return $this;
    }

    /**
     * Get upload folder path
     *
     * @return string
     */
    public function getUploadPath ()
    {
        return $this->uploadPath;
    }

    /**
     * Set upload folder path
     *
     * @param string $uploadPath
     * @return Options
     */
    public function setUploadPath ($uploadPath)
    {
        $this->uploadPath = $uploadPath;
        return $this;
    }

    /**
     *
     * @return the $baseUri
     */
    public function getBaseUri ()
    {
        return $this->baseUri;
    }

    /**
     *
     * @param string $baseUri
     */
    public function setBaseUri ($baseUri)
    {
        $this->baseUri = $baseUri;
        return $this;
    }

    /**
     *
     * @return the $settings
     */
    public function getSettings ()
    {
        return $this->settings;
    }

    /**
     *
     * @param multitype: $settings
     */
    public function setSettings ($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Get the unique settings fields
     *
     * @return array
     */
    public function getFields ()
    {
        $settings = $this->getSettings();
        $fields = array_key_exists('fields', $settings) ? array_unique($settings['fields']) : [];
        return $fields;
    }

    /**
     * Get the profile photo placeholder path/url
     *
     * @return string $profilePhotoPlaceholder
     */
    public function getProfilePhotoPlaceholder ()
    {
        return $this->profilePhotoPlaceholder;
    }

    /**
     * Set the profile photo placeholder path/url
     *
     * @param string $profilePhotoPlaceholder
     */
    public function setProfilePhotoPlaceholder ($profilePhotoPlaceholder)
    {
        $this->profilePhotoPlaceholder = $profilePhotoPlaceholder;
        return $this;
    }
}