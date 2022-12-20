<?php
/**
 * Autoload all these functions on launch
 *  @copyright  Copyright (c) 2012 Savv-e.com.au
 * @category : Zend
 */
return function ($class) {
    static $map;
    if (!$map) {
        $map = include __DIR__ . '/autoload_classmap.php';
    }

    if (!isset($map[$class])) {
        return false;
    }
    return include $map[$class];
};
