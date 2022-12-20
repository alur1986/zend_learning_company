<?php

namespace Agent\View\Helper;

use Savve\View\Helper\Service;
use Savvecentral\Entity;
use Savve\View\Helper\AbstractViewHelper;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class GetAgencyName extends AbstractViewHelper  implements
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Agent Service
     *
     * @var \Agent\Service\AgentService
     */
    protected $service;

    /**
     * Agent Name
     *
     * @var $name
     */
    protected $name;

    /**
     * Constructor
     *
     */
    public function __construct ( $service )
    {
        //$pluginManager =  $this->getServiceLocator();
        //$serviceManager = $pluginManager->getServiceLocator();
        // $serviceManager = $this->getServiceManager();
        $this->service = $service; // $serviceManager->get('Agent\Service\AgentService');
    }

    /**
     * Get the agency name that matched the provided code
     *
     * @param $siteId
     * @param null|string $code
     * @return mixed
     */
    public function __invoke ($siteId, $code = null)
    {
        if ($siteId && $code) {
            $this->name = $this->service->findOneAgentNameBySite( $siteId, $code );
//	    if ($code == 'MTB001') var_dump($this->name);
        }

        return $this->name;
    }
}
