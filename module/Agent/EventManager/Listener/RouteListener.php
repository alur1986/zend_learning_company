<?php

namespace Agent\EventManager\Listener;

use Savve\Mvc\Controller\AbstractRestfulController;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\JsonModel;

class RouteListener {
	/**
	 * Constant for guard that can be added to the MVC event result
	 *
	 * @var string
	 */
	const UNAUTHORISED = 'unauthorised';

	/**
	 * Unauthorised access error message
	 *
	 * @var string
	 */
	const UNAUTHORISED_MESSAGE = 'Your site is not authorised to access this module. Please contact Savve to arrange access for your platform or site.';


	protected $serviceLocator = null;

	/**
	 * Set service locator
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		return $this;
	}

	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator ()
	{
		return $this->serviceLocator;
	}

	/**
	 * MvcEvent::EVENT_ROUTE event listener
	 *
	 * @param MvcEvent $event
	 * https://dev.twitter.com/oauth/overview/authorizing-requests
	 * oauth_consumer_key = 3406cb2b4efc2e5a26de3b8d9ac6b263

	* This is the Savvecentral consumer key. Other applications are not supported currently (Required)
	*
	* oauth_ttl = Time for the request to be alive. Default is 3 minutes (Not Required)
	*
	* oauth_timestamp : Send in the current timestamp in seconds to validate the request (Required)
	*
	* oauth_version : Currently we only support version 1.0 of oauth. (Not Required)
	*
	* oauth_nonce : Find a way of creating randomness. If this key exists in our db your request fails . (Required)
	*
	* oauth_signature_method : The method used for creating the signature. By default it is HMAC-SHA1 (Not Required)
	*
	* oauth_signature : TODO (Required)
	*
	* oauth_token : Token to identify the user within Savvecentral. This is usually the impersonate authentication token if used within Savvecentral. Public apis are currently not supported (Required)
	*
	* simulation_mode : Prevent updates (Not Required) (Yes/No)
	 */
	public function routeListener (MvcEvent $event)
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
		/* @var $authentication \Zend\Authentication\AuthenticationService */
		/* @var $learner \Savvecentral\Entity\Learner */
		/* @var $options \Authorization\Service\Options */
		/* @var $authorization \Authorization\Service\AuthorizationService */
		/* @var $role \Savvecentral\Entity\AccessRoles */
		/* @var $controllerManager \Zend\Mvc\Controller\ControllerManager */
		$controller = $event->getTarget ();
		$application = $event->getApplication ();
		$eventManager = $application->getEventManager ();
		$serviceManager = $application->getServiceManager ();
		$routeMatch = $event->getRouteMatch ();
		$request = $event->getParam ('request') ?: $controller->getRequest ();
		$response = $event->getParam ('response') ?: $controller->getResponse ();
		$authentication = $serviceManager->get ('Zend\Authentication\AuthenticationService');
		$options = $serviceManager->get ('Authorization\Options');
		$controllerManager = $serviceManager->get ('ControllerManager');

		$this->setServiceLocator ($serviceManager);


		// only process if routeMatch is set
		if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch || $request instanceof \Zend\Http\PhpEnvironment\Request)) {
			return;
		}

		//get information on the role
		$routeName = $routeMatch->getMatchedRouteName ();
		$roleId = $routeMatch->getParam ('role_id');

		// get the site details
		$site     = $serviceManager->get('Site');
		$settings = $site->getSettings();

		// get the route
		$routeMatch = $event->getRouteMatch();
		$allowAgent = false;

		foreach ($settings as $setting) {
			if ($setting['name'] == 'show_agents') {
				$allowAgent = $setting['value'];
			}
		}

		if ($routeName == 'agent' || strpos($routeName, 'agent/') !== false) {

			// Disable access to this route if it's not enabled for this site.
			if ($allowAgent == false) {

				// if you reach here, the user role does NOT have grant access to the MVC event
				$event->setError (static::UNAUTHORISED);
				$event->setParam ('exception', new Exception\UnauthorisedException(static::UNAUTHORISED_MESSAGE, 401));

				// do not continue the rest of the event listeners
				$event->stopPropagation (true);

				// dispatch the DISPATCH_ERROR event
				$eventManager->trigger (MvcEvent::EVENT_DISPATCH_ERROR, $event);

			}
		}
	}
}