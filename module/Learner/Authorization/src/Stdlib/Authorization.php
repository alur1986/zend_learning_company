<?php

namespace Authorization\Stdlib;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;

abstract class Authorization
{
    /**
     * Guest Level
     *
     * @var integer
     */
    const LEVEL_1 = 1;

    /**
     * Learner Level
     *
     * @var integer
     */
    const LEVEL_2 = 100;

    /**
     * Group Level
     *
     * @var integer
     */
    const LEVEL_3 = 500;

    /**
     * Learning Creator Level
     *
     * @var integer
     */
    const LEVEL_4 = 600;

    /**
     * Learning Admin Level
     *
     * @var integer
     */
    const LEVEL_5 = 700;

/**
    * Company Level
    *
    * @var integer
    */
    const LEVEL_6 = 55555;

    /**
    * Platform Level
    *
    * @var integer
    */
    const LEVEL_7 = 77777;

    /**
     * Super Level
     *
     * @var integer
     */
    const LEVEL_SUPER = 99999;

    /**
     * Allow permission
     *
     * @var string
     */
    const ALLOW = 'allow';

    /**
     * Deny permission
     *
     * @var string
     */
    const DENY = 'deny';

    /**
     * The Authorization module service
     *
     * @var \Authorization\Service\AuthorizationService
     */
    private static $authorizationService = null;

    /**
     * Get the instantiated instance of the Authorization service
     *
     * @return \Authorization\Service\AuthorizationService
     * @throws Exception\RuntimeException
     */
    static public function getInstance ()
    {
        if (null === self::$authorizationService) {
            throw new Exception\RuntimeException(sprintf('Authorization service not initialised'), null, null);
        }

        return self::$authorizationService;
    }

    /**
     * Set the Authorization service
     *
     * @param \Authorization\Service\AuthorizationService $authorizationService
     */
    static public function setInstance ($authorizationService)
    {
        self::$authorizationService = $authorizationService;
    }

    /**
     * Get the current user's role
     *
     * @return Entity\AccessRoles
     */
    static public function role ()
    {
        $authorization = self::getInstance();
        return $authorization->getRole();
    }

    /**
     * Get the current role's permissions
     *
     * @return array
     */
    static public function permissions ()
    {
        $role = static::role();
        $permissions = $role['permissions'];
        return $permissions;
    }

    static public function isGranted ($permission, $type = 'route')
    {
    }
}