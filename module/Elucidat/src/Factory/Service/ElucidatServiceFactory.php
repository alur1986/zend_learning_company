<?php

namespace Elucidat\Elucidat\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ElucidatServiceFactory implements
    FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('Elucidat\Elucidat\Options');
        $baseUri = $options['base_uri'];
        $clientId = $options['client_id'];
        $clientSecret = $options['client_secret'];
        $username = $options['username'];
        $password = $options['password'];
        $baseUrl  = $options['base_url'];

        $projectUrl = $options->getProjectUrl();

        $client = new \Elucidat\Elucidat\Client\Unirest();
        $elucidat = new \Elucidat\Elucidat\Elucidat($client);

        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $elucidat->setEntityManager($entityManager);

        $elucidat->setUrl($baseUri)
            ->setClientId($clientId)
            ->setClientSecret($clientSecret)
            ->setUsername($username)
            ->setPassword($password)
			->setBaseUrl($baseUrl)
            ->setProjectUrl($projectUrl);

        return $elucidat;
    }
}