<?php

namespace Elucidat\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class PaidElucidatLicencesFactory implements
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
        $clients = $service->retrieve();

        //retrieve the paid accounts from the response returned
    //    $paidAccounts = ($clients && count($clients['legacy_paid_account'])>0)?$clients['legacy_paid_account'] : [];
        $paidAccounts = ($clients && count($clients['contract_agreed'])>0)?$clients['contract_agreed'] : [];

        //Free accounts for savvy needs to be listed
        $freeAccounts = ($clients && count($clients['free_account'])>0)?$clients['free_account'] : [];

        return array_merge($paidAccounts, $freeAccounts);
    }
}