<?php

namespace Company\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class CompanyDataServiceFactory implements
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
        $platformId = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch()
            ->getParam('platform_id');


        /* @var $service \Company\Service\CompanyService */
        $service = $serviceLocator->get('Company\Service');
        $companies = $service->findAllDataByPlatformId($platformId);

        return $companies;
    }
}