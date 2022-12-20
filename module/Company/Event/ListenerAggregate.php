<?php
/**
 * @deprecated
 */
namespace Company\Event;

use Savve\EventListenerManager\AbstractListenerAggregate;
use Savve\Form\Exception as FormException;
use Zend\Db\Sql\Expression;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\View\ViewEvent;
use Zend\Stdlib\Hydrator\Aggregate\HydrateEvent;
use Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;
use Zend\EventManager\EventManagerInterface;

class ListenerAggregate extends AbstractListenerAggregate
{

    /**
     * Attach one or more listeners
     *
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach (EventManagerInterface $event)
    {
        /* @var $sharedEventManager \Zend\EventManager\SharedEventManager */
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */

        $sharedEventManager = $event->getSharedManager();
        $serviceManager = $this->getServiceManager();

        // HYDRATEEVENT listeners
        $this->listeners[] = $sharedEventManager->attach('Company\Hydrator\Company', HydrateEvent::EVENT_HYDRATE, [ $this, 'hydrateCompany' ], 100);
	}

	/**
	 * Company\Hydrator\Company HYDRATEEVENT listener
	 * @param HydrateEvent $event
	 */
	public function hydrateCompany (HydrateEvent $event)
	{
	    /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
	    $router = clone $this->service('Router');
	    $filterScheme = $this->filter('UrlScheme');
	    $filterHostname = $this->filter('Hostname');

	    $data = $event->getHydrationData();
	    $object = $event->getHydratedObject();

	    // create company permalinks
	    if (isset($data['company_id']) && !empty($data['company_id'])) {
	        $data['view_url'] = $router->assemble(['company_id' => $data['company_id']], ['name' => 'company/view']);
	        $data['edit_url'] = $router->assemble(['company_id' => $data['company_id']], ['name' => 'company/edit']);
	    }

	    // create company permalink
	    if (isset($data['url']) && !empty($data['url'])){
	        // normalise url, add url scheme
	        $data['url'] = $filterHostname->filter($data['url']);
	        $baseUrl = $filterScheme->filter($data['url']);

	        $config = $this->service('Config');
	        $routerConfig = $config['router'];
	        $companyRouter = \Zend\Mvc\Router\Http\TreeRouteStack::factory($routerConfig);
	        $companyRouter->setBaseUrl($baseUrl);
	        $data['company_url'] = $companyRouter->assemble([], ['name' => 'home']);
	        $data['permalink'] = $companyRouter->assemble([], ['name' => 'company']);
	        $data['view_url'] = $companyRouter->assemble([], ['name' => 'company/view']);
	        $data['edit_url'] = $companyRouter->assemble([], ['name' => 'company/edit']);


	        // if logged in
	        /* @var $authentication \Zend\Authentication\AuthenticationService */
	        $authentication = $this->service('Zend\Authentication\AuthenticationService');
	        $identity = $authentication->hasIdentity() ? (string) $authentication->getIdentity() : null;
	        if ($identity) {
	            $learner = $this->service('Learner');
	            $authenticationToken = $learner['authentication_token'];
		        $data['autologin'] = $companyRouter->assemble(['authentication_token'=> $authenticationToken], ['name' =>  'learner/autologin']);
	        }

	    }

	    if (isset($data['website']) && !empty($data['website'])){
	        $data['website'] = $filterScheme->filter($data['website']);
	    }

	    // set the data back to the event params
	    $event->setHydrationData($data);
	}
}