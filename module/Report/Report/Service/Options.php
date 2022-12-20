<?php

namespace Report\Service;

use Savve\Stdlib\AbstractOptions;

class Options extends AbstractOptions
{

    /**
     * Directory path of the report files
     *
     * @var string
     */
    protected $directoryPath;

    /**
     *
     * @return the $directoryPath
     */
    public function getDirectoryPath ()
    {
        return $this->directoryPath;
    }
}