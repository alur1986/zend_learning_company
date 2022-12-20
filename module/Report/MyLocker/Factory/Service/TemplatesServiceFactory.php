<?php

namespace Report\MyLocker\Factory\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class TemplatesServiceFactory implements
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
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!$routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            return false;
        }
        $siteId = $routeMatch->getParam('site_id');
        $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
        $reportType = 'report-mylocker';

        // retrieve templates from the database
        /* @var $templateService \Report\Service\TemplateService */
        $templateService = $serviceLocator->get('Report\TemplateService');
        $templates = $templateService->findAllTemplatesBySiteId($siteId, $reportType);

        return $templates;
    }
}