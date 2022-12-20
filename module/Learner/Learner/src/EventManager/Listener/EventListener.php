<?php

namespace Learner\EventManager\Listener;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;

class EventListener
{

    /**
     * Injects the logged in user's user_id onto the routeMatch
     *
     * @param MvcEvent $event
     */
    public function postRoute (MvcEvent $event)
    {
        /* @var $controller \Zend\Mvc\Controller\AbstractActionController */
        /* @var $application \Zend\Mvc\Application */
        /* @var $eventManager \Zend\EventManager\EventManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        /* @var $viewManager \Zend\Mvc\View\Http\ViewManager */
        /* @var $viewHelperManager \Zend\View\HelperPluginManager */
        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        /* @var $response \Zend\Http\PhpEnvironment\Response */
        /* @var $controllerPluginManager \Zend\Mvc\Controller\PluginManager */
        /* @var $translator \Zend\I18n\Translator\Translator */
        /* @var $headMeta \Zend\View\Helper\HeadMeta */
        /* @var $flashMessenger \Zend\Mvc\Controller\Plugin\FlashMessenger */

        $controller = $event->getTarget();
        $application = $event->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $viewManager = $serviceManager->get('ViewManager');
        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $renderer = $viewHelperManager->getRenderer();
        $routeMatch = $event->getRouteMatch();
        $router = $serviceManager->get('Router');
        $request = $event->getParam('request') ?  : $controller->getRequest();
        $response = $event->getParam('response') ?  : $controller->getResponse();
        $controllerPluginManager = $serviceManager->get('ControllerPluginManager');
        $translator = $serviceManager->get('Translator');
        $headMeta = $renderer->headMeta();

        // only process if routeMatch is set
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && $request instanceof \Zend\Http\PhpEnvironment\Request)) {
            return;
        }
        $routeName = $routeMatch->getMatchedRouteName();

        // if learner is logged in, nothing to do, allow to proceed to the dashboard
        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
        $identity = $authentication->getIdentity();

        if ($identity instanceof Entity\Learner) {

            // set the learner's locale
            $locale = isset($identity['locale']) && $identity['locale'] ? $identity['locale'] : ($translator->getLocale() ? $translator->getLocale() : locale_get_default());
            $timezone = isset($identity['timezone']) && $identity['timezone'] ? $identity['timezone'] : date_default_timezone_get();

            // set browser content language
            $headMeta->appendHttpEquiv('Content-Language', $locale);

            // set the translator locale
            $translator->setLocale($locale);

            // set the view helper translator locale
            $viewHelperManager->get('translate')
                ->getTranslator()
                ->setLocale($locale);

            /* @var $dateFormat \Savve\View\Helper\DateFormat */
            $dateFormat = $viewHelperManager->get('dateFormat');
            $dateFormat->setTimezone($timezone);
            $dateFormat->setLocale($locale);
        }
    }
}