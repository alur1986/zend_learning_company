<?php

namespace Learner\Permissions;

use Savvecentral\Entity;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\AssertionInterface;

class AssertPlatformIdMatches implements
        AssertionInterface
{

    /**
     * Platform ID
     *
     * @var integer
     */
    protected $platformId;

    /**
     * Platform doctrine entity
     *
     * @var Entity\Platform
     */
    protected $platform;

    /**
     * Constructor
     *
     * @param integer $platformId
     */
    public function __construct ($platformId)
    {
        $this->platformId = $platformId;
    }

    /**
     * Set platform doctrine entity
     *
     * @param Entity\Platform $platform
     * @return AssertPlatformIdMatches
     */
    public function setPlatform ($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * Assertion method - must return a boolean.
     *
     * @param Rbac $rbac
     * @return bool
     */
    public function assert (Rbac $rbac)
    {
        return false;
    }
}