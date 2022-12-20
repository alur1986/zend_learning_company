<?php

namespace Authentication\Storage;

use Zend\Authentication\Storage\Session as ZendSessionStorage;

class Session extends ZendSessionStorage
{

    /**
     * Set remember me TTL
     *
     * @param integer $time
     * @return \Authentication\Storage\Session
     */
    public function rememberMe ($time)
    {
        $session = $this->session;
        $sessionManager = $session->getManager();
        $sessionManager->rememberMe($time);
        return $this;
    }

    /**
     * Forget me
     *
     * @return \Authentication\Storage\Session
     */
    public function forgetMe ()
    {
        $session = $this->session;
        $sessionManager = $session->getManager();
        $sessionManager->forgetMe();
        return $this;
    }

    /**
     *
     * @return \Zend\Session\Container
     */
    public function getSession ()
    {
        return $this->session;
    }
}