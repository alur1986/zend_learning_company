<?php

namespace Savvecentral\Translations;

use Savve\Mvc\AbstractModule;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
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