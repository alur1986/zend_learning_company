<?php

namespace Elucidat\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class SavvecentralElucidatLicencesFactory implements
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
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $serviceLocator->get('Elucidat\Elucidat');
        $accounts = $service->findAllAccounts();
        return $accounts ? $accounts : [];
    }
}