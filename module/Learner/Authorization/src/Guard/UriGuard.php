<?php

namespace Authorization\Guard;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;

class UriGuard extends AbstractGuard
{
    /**
     * MVC event to listen
     *
     * @var string
     */
    const EVENT_NAME = 'isAllowed';

    /**
     * Default access permission
     *
     * @var string
     */
    protected $permission = self::ALLOW;

    /**
     * Collection of rules for the current logged in role
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Constructor
     *
     * @param array|\Traversable $permissions
     */
    public function __construct ($rules)
    {
        ksort($rules);
        $this->rules = $rules;
    }

    /**
     * Navigation menu listener
     * Zend\View\Helper\Navigation\AbstractHelper
     */
    /*
    public function onNavigation (Event $event)
    {
        /* @var $viewHelper \Zend\View\Helper\AbstractViewHelper */
        /* @var $pluginManager \Zend\View\HelperPluginManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager

        $viewHelper = $event->getTarget();
        $serviceManager = $pluginManager = $viewHelper->getServiceLocator();
        //$serviceManager = $pluginManager->getServiceLocator();

        // @todo get this default $granted value from the config
        $granted = false;
        $rules = $this->rules;

        // get the current navigation page
        /* @var $page \Zend\Navigation\Page\Uri
        $page = $event->getParam('page');

        // process only menus that are of URI type
        if ($page instanceof \Zend\Navigation\Page\Uri) {
            $pageUri = $page->getUri();

            $allow = self::ALLOW;

            // find the current page route from the rules
            $found = array_filter($rules, function  ($permission, $uri) use( $pageUri)
            {
               return fnmatch($uri, $pageUri, FNM_CASEFOLD) && strtolower($permission) === 'allow';
            }, ARRAY_FILTER_USE_BOTH);

            // if found, then grant permission
            if ($found) {
                $granted = true;
           }

            // do not process other event listeners after this
            if ($granted === false) {
                $event->stopPropagation(true);
            }

            return $granted;
        }
        return true;
    }
    */
}
