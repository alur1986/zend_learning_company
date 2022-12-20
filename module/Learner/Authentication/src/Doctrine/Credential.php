<?php

namespace Authentication\Doctrine;

use Savvecentral\Entity;

class Credential
{

    /**
     * Invoke class
     *
     * @param Entity\Learner $learner
     * @param string $passwordGiven
     */
    static public function setCredential (Entity\Learner $learner, $passwordGiven)
    {
        $salt = $learner['salt'];

        // assuming that the salt is null or empty, then a plaintext password was set
        if ($learner['password'] === $passwordGiven) {
            return true;
        }

        // using Bcrypt
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        $bcrypt->setSalt($salt);

        // $passwordGiven is unhashed password that inputted by user
        // $learner->getPassword() is hashed password that saved in db
        $verified = $bcrypt->verify($passwordGiven, $learner['password']);

        return $verified;
    }
}