<?php

namespace Elucidat;

use Savve\Mvc\AbstractModule;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventInterface;
use Zend\EventManager\StaticEventManager;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Module class
 */
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
