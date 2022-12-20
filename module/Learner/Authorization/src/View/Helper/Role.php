<?php

namespace Authorization\View\Helper;

use Savvecentral\Entity;
use Savve\View\Helper\AbstractViewHelper;

class Role extends AbstractViewHelper
{

    /**
     * Role
     *
     * @var Entity\AccessRoles
     */
    private $role;

    /**
     * Constructor
     *
     * @param Entity\AccessRoles|string $role
     */
    public function __construct ($role)
    {
        $this->role = $role;
    }

    /**
     * Returns the current learner's role
     *
     * @return Entity\AccessRoles
     */
    public function __invoke ()
    {
        // if role is a string, retrieve the role data from repository
        if (is_string($this->role)) {
            $serviceManager = $this->getServiceManager();

            /* @var $service \Authorization\Service\AuthorizationService */
            $service = $serviceManager->get('Zend\Authorization\AuthorizationService');
            $this->role = $service->findOneRoleByName($this->role);
        }

        return $this->role;
    }
}