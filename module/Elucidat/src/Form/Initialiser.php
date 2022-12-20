<?php

namespace Elucidat\Form;

use Savve\Stdlib;
use Zend\Form;
use Zend\Form\Element;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class Initialiser implements
        InitializerInterface
{

    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize ($instance, ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $serviceLocator->getServiceLocator();

        // get the route params
        $routeMatch = $serviceManager->get('Application')->getMvcEvent()->getRouteMatch();

        if (!$routeMatch) {
            return;
        }


        if ($instance instanceof Form\Fieldset) {
            if($instance->has('elucidat_customer_code')){
                $element = $instance->get(('elucidat_customer_code'));
                if($element instanceof Element\Select || $element instanceof Element\MultiCheckbox) {
                    /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
                    $serviceManager = $serviceLocator->getServiceLocator();
                    $paidAccounts = $serviceManager->get('Elucidat\PaidElucidatLicences');
                    if($paidAccounts && count($paidAccounts) > 0){
                        $data = $element->getValueOptions();
                        foreach($paidAccounts as $account){
                            $item = [];
                            $item['label'] = sprintf("%s (%s)",(isset($account['company_name']) ? $account['company_name'] : ""),$account['company_email']);
                            $item['value'] = $account['customer_code'];
                            $item = Stdlib\ArrayUtils::merge($item, $paidAccounts);
                            $data[] = $item;
                        }
                    }
                    $element->setValueOptions($data);
                }
            }
        }
    }
}
