<?php

namespace Agent;

use Savve\Mvc\AbstractModule;
use Zend\EventManager\EventInterface;

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