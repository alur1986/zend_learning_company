<?php

namespace Elucidat\Elucidat\Service;

use Savve\Stdlib\AbstractOptions;

class Options extends AbstractOptions
{
    /**
     * Get the project URl
     * @return string
     */
    public function getProjectUrl(){
        return $this->baseUri."projects";
    }
}