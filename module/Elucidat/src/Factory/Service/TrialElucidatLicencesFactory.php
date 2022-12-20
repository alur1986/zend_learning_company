<?php

namespace Elucidat\Factory\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class TrialElucidatLicencesFactory implements
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

        //retrieve the trial accounts from the response returned
        $trialAccounts = ($clients && count($clients['trial'])>0)?$clients['trial'] : null;
        return $trialAccounts ? $trialAccounts : [];
    }
}