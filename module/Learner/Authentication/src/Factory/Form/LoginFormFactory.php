<?php

namespace Authentication\Factory\Form;

use \ArrayObject as Object;
use Authentication\Hydrator\Authentication as Hydrator;
use Authentication\InputFilter\Authentication as InputFilter;
use Authentication\Form\LoginForm as Form;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LoginFormFactory implements
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

        $hydrator = new Hydrator();
        $inputFilter = new InputFilter();
        $object = new Object();

        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $serviceManager->get('Router');
        $url = $router->assemble([], [
            'name' => 'login'
        ]);

        // form
        $form = new Form('login');
        $form->setLabel('Login');
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setAttribute('action', $url);
        $form->setObject($object);

        // validation group
        $form->setValidationGroup([
            'identity',
            'password',
            'redirect_url',
            'remember_me'
        ]);

        // prepopulate redirect_url with current URL
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if ($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch) {
            $routeName = $routeMatch->getMatchedRouteName();

            // get the current full URL
            // $helper = new \Zend\View\Helper\ServerUrl();
            // $redirectUrl = $helper(true);
            $redirectUrl = Stdlib\HttpUtils::detectUrl();

            // we are redirecting only when the current page is not the login page
            if ($form->has('redirect_url') && fnmatch('*login*', $routeName, FNM_CASEFOLD) === false) {
                $form->get('redirect_url')
                    ->setValue($redirectUrl);
            }
        }

        return $form;
    }
}