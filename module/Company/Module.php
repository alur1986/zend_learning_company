<?php

namespace Company;

use Savve\Mvc\AbstractModule;

class Module extends AbstractModule
{

    /**
     * Define module dependencies
     *
     * @var array
     */
    protected $moduleDependencies = [
        'Savve'
    ];
}