<?php

namespace Authorization\Guard;

use Savvecentral\Entity;
use Savve\BlockManager\AbstractBlock;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\View\Model\ViewModel;
use Zend\View\Model\ModelInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;

class ViewModelGuard extends AbstractGuard
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
     * Zend\View\Model\ViewModel event listener
     *
     * @return boolean $accepted True if current role is granted access to the route, False otherwise
     */
    public function onViewRender (Event $event)
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        /* @var $pluginManager \Zend\View\HelperPluginManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

        $renderer = $event->getTarget();
        $pluginManager = $renderer->getHelperPluginManager();
        $serviceManager = $pluginManager->getServiceLocator();

        // get the current view model template name
        $model = $event->getParam('model');
        $templateName = $model instanceof \Zend\View\Model\ModelInterface ? $model->getTemplate() : $model;
        $values = $event->getParam('values');

        $granted = true;
        $found   = false;
        $rules = $this->rules;
   
        ksort($rules);

        // find the current page route from the rules
        $found = array_filter($rules, function  ($permission, $template) use ($templateName)
        {
            return fnmatch($template, $templateName, FNM_CASEFOLD) && strtolower($permission) === 'deny';
        }, ARRAY_FILTER_USE_BOTH);

        // if found, then dissallow permission
        if ($found) {
            $granted = false;
        }

        // do not process other event listeners after this
        if ($granted === false) {
            $event->stopPropagation(true);
            $model = '';
            return $model;
        }
    }
}
