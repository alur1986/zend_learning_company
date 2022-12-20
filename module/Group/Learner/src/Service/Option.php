<?php

namespace Group\Learner\Service;

use Savve\Stdlib\AbstractOptions;

class Option extends AbstractOptions
{

    /**
     * File upload path
     *
     * @var string
     */
    protected $uploadPath;

    /**
     * Get upload path
     *
     * @return the $uploadPath
     */
    public function getUploadPath ()
    {
        return $this->uploadPath;
    }

    /**
     * Set upload path
     *
     * @param string $uploadPath
     * @return Option
     */
    public function setUploadPath ($uploadPath)
    {
        $this->uploadPath = $uploadPath;
        return $this;
    }
}