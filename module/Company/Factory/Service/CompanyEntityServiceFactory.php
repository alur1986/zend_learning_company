<?php

namespace Company\Factory\Service;

use Zend\Console\Request as ConsoleRequest;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CompanyEntityServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        // we are only processing this when not in console
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $serviceLocator->get('Request');
        if (!($request instanceof \Zend\Http\PhpEnvironment\Request) || $request instanceof ConsoleRequest) {
            return false;
        }

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        $companyId = $routeMatch->getParam('company_id');

        /* @var $service \Company\Service\CompanyService */
        $service = $serviceLocator->get('Company\Service');
        $company = $service->findOneByCompanyId($companyId);

        return $company ? $company : false;
    }
}