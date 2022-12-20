<?php

namespace Learner\Factory\View\Helper;

use Learner\View\Helper\Profile as Helper;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Image\Adapter\Imagine as Adapter;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ProfileHelperFactory implements
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
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return false;
        }
        $learnerId = $routeMatch->getParam('user_id');
        $options = $serviceManager->get('Savve\ImageOptions');
        $savePath = $options->getSavePath() . DIRECTORY_SEPARATOR . 'learner' . DIRECTORY_SEPARATOR . $learnerId;
        $subdomain = false;
        $hostname = HOSTNAME;
        if (APPLICATION_ENV != 'development') {
            $arr = explode(".", HOSTNAME);
            $subdomain = array_shift($arr);
            $subdomain .= "/";
            $hostname = str_replace($subdomain, "s1", HOSTNAME);
        }
        $baseUri = Stdlib\HttpUtils::detectScheme() . '://' . str_ireplace('//', '/', $hostname . '/' . $subdomain . $options->getBaseUri() . '/learner/' . $learnerId);
        if (strpos($baseUri, "cache/img/learner") !== false && $subdomain != 'www') {
            $baseUri = Stdlib\HttpUtils::detectScheme() . '://' . str_ireplace('//', '/', $hostname . $options->getBaseUri() . '/learner/' . $learnerId);
        }
        $adapter = new Adapter();
        $helper = new Helper($adapter, $savePath, $baseUri);
        return $helper;
    }
}