<?php

namespace Elucidat\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class AllElucidatLicencesFactory implements
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
        $allAccounts = array_merge((($clients && count($clients['contract_agreed'])>0)?$clients['contract_agreed'] : []),
            (($clients && count($clients['trial'])>0)?$clients['trial'] : []) , (($clients && count($clients['free_account'])>0)?$clients['free_account'] : []));

        return $allAccounts ? $allAccounts : [];
    }
}