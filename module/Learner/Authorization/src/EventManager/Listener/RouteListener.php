<?php

namespace Authorization\EventManager\Listener;

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
	const UNAUTHORISED_MESSAGE = 'You are not authorised to access this role';


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

		// check if the Authorization submodule is "enabled"
		if ($options->getEnabled () !== true) {
			return;
		}

		$authorization = $serviceManager->get ('Zend\Authorization\AuthorizationService');

		// only process if routeMatch is set
		if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch || $request instanceof \Zend\Http\PhpEnvironment\Request)) {
			return;
		}

		// get details about current learner
		$identity = $authentication->getIdentity ();
		$learner = $identity instanceof Entity\Learner ? $identity : null;
		$learnerRole = $authorization->getRole ();

		//get information on the role
		$routeName = $routeMatch->getMatchedRouteName ();
		$roleId = $routeMatch->getParam ('role_id');

		// Disable authorisation if the controller is an instance of restful controller.
		// Api calls are authenticated through oauth
		if (($controllerManager->get ($routeMatch->getParam ('controller'))) instanceof AbstractRestfulController) {
			$query = $request->getQuery ();

			/** @var \SavvecentralApi\V1\Service\Options $apiOptions */
			$apiOptions = $serviceManager->get ('SavvecentralApi\V1\Options');

                        /** @var \SavvecentralApi\V1\Service\Savvecentral3Api $apiService */
                        $apiService = $serviceManager->get ('SavvecentralApi\V1\Service');

			$event->getRouteMatch()->setParam('REST',true);

                        $jwtToken = $request->getHeader ('Authorization');
                        if (!empty($jwtToken) && strpos($jwtToken->getFieldValue(), 'Bearer ') === 0) {
                            $jwtToken = str_replace('Bearer ', '', $jwtToken->getFieldValue());
                            $siteId = $routeMatch->getParam ('site_id');
                            $result = $apiService->processJwtTokenRequest($jwtToken, $siteId); 
                        } else {
			    // https://dev.twitter.com/oauth/overview/authorizing-requests

			    //There is two ways parameters can be  fed in : Through a query or through the headers. Headers take preference over the query
			    $applicationKey = $request->getHeader ('oauth_consumer_key') ? : $query->get ('oauth_consumer_key');

			    //This will be replaced by application secrets. Right now only application secret of savvecentral is allowed
			    //$applicationSecret = $apiOptions->getImplicitSecretkey();

			    //Nonce is a random string passed to the api call to make sure the call is not repeated by mistake
			    $nonce = $request->getHeader ('oauth_nonce') ?: $query->get ('oauth_nonce');
			    //Signature : Signed signature
			    $signature = $request->getHeader ('oauth_signature') ?: $query->get ('oauth_signature');
			    // Time stamp : Timestamp (in seconds) as to when the call was created)
			    $timestamp = $request->getHeader ('oauth_timestamp') ?: $query->get ('oauth_timestamp');
			    //Token is the key for authorizing a user
			    $token =  $request->getHeader ('oauth_token') ?: $query->get ('oauth_token');
			    //SignatureMethod : The method used for creating the signature. By default it is HMAC-SHA1
			    $signatureMethod = $request->getHeader ('oauth_signature_method') ?: ($query->get ('oauth_signature_method')?:'HMAC-SHA1');
			    //vERSION
			    $version = $request->getHeader ('oauth_version') ?: ($query->get ('oauth_version')?:'1.0');
			    //VERSION IS ignored for now. SC3 only supports oauth v1
			    $ttl = intval ($request->getHeader ('oauth_ttl') ?: (($query->get ('oauth_ttl')) ?: $apiOptions->getDefaultTtl ()));
			    //Simulation mode
			    $simulationMode = $request->getHeader ('simulation_mode') ?: ($query->get ('simulation_mode')?:'No');

			    if (is_nan ($ttl) || $ttl > $apiOptions->getMaxTtl()) {
				    //time to leave is set to a max of 600
				    $ttl = $apiOptions->getDefaultTtl ();
			    }

			    /** @var \Zend\View\Helper\Url $urlHelper */
			    $urlHelper =$this->getServiceLocator()->get('ViewHelperManager')->get('url');

			    $allParameters= array_merge(Stdlib\ObjectUtils::toArray($request->getPost()),Stdlib\ObjectUtils::toArray($request->getQuery()));
			    $result = $apiService->processRequest($request->getMethod(),$urlHelper(),$applicationKey,$nonce,$signature,$timestamp,$token,$allParameters,$signatureMethod,$version,$ttl,$simulationMode);
			}

			if(is_array($result)){
				return $apiOptions->generateRestResponse($result['key'], $event, $eventManager, $result['comments'],$simulationMode);
			}
			else {
				return $apiOptions->generateRestResponse($result, $event, $eventManager, '',$simulationMode);
			}

		}
		else {
			//Normal action controller
			// only process if requesting the role
			if (!($roleId && $learner && $learnerRole)) {
				return;
			}
			// get the current logged in learner's role level
			$learnerRoleLevel = isset($learnerRole['level']) ? $learnerRole['level']['id'] : 0;

			// get the requested role
			$role = $authorization->findOneRoleById ($roleId);
			$level = isset($role['level']) ? $role['level']['id'] : 0;

			// if current logged in learner is not allowed to edit the current requested role, send a dispatch error
			if ($level <= $learnerRoleLevel === false) {
				// set HTTP error status code to 401-Unauthorised
				$response->setStatusCode (401);

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
