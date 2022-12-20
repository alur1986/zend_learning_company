<?php

namespace Authentication\View\Helper;

use Savvecentral\Entity;
use Savve\View\Helper\AbstractViewHelper;

class LoggedIn extends AbstractViewHelper
{

    /**
     * Invoke the view helper
     *
     * @return Entity\Learner boolean
     */
    public function __invoke ()
    {
        $serviceManager = $this->getServiceManager();

        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');
        if ($authentication->hasIdentity()) {
            return $authentication->getIdentity();
        }

        return false;
    }
}