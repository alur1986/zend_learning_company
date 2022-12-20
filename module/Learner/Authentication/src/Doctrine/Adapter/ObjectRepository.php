<?php

namespace Authentication\Doctrine\Adapter;

use DoctrineModule\Authentication\Adapter\ObjectRepository as AbstractObjectRepository;
use Zend\Authentication\Result as AuthenticationResult;

class ObjectRepository extends AbstractObjectRepository
{

    /**
     * {@inheritDoc}
     */
    public function authenticate ()
    {
        $this->setup();
        $options = $this->options;
        $identityColumn = $options->getIdentityProperty();

        /* @var $repository \Savve\Doctrine\Repository\AbstractRepository */
        $repository = $options->getObjectRepository();
        $identity = $repository->findOneBy([  $identityColumn => $this->identity, 'status' => [ 'new', 'active' ] ]);

        if (!$identity) {
            $this->authenticationResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticationResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
            return $this->createAuthenticationResult();
        }
        /* @var $authResult \Zend\Authentication\Result */
        $authResult = $this->validateIdentity($identity);

        return $authResult;
    }
}