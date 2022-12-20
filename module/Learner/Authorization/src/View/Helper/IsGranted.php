<?php

namespace Authorization\View\Helper;

use Authorization\Stdlib\Authorization;
use Savvecentral\Entity;
use Savve\View\Helper\AbstractViewHelper;

class IsGranted extends AbstractViewHelper
{
    /**
     * Collection of rules for the current logged in role
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Constructor
     *
     * @param array|\Traversable $permissions
     */
    public function __construct ($rules)
    {
        $this->rules = $rules;
    }

    /**
     * Check if the current resource has permission granted
     *
     * @param string $resource
     * @param string $type
     * @return boolean
     */
    public function __invoke ($resource, $type = 'block')
    {
        $isGranted = $this->isGranted($resource, $type);
        return $isGranted;
    }

    /**
     * Check if the current resource has permission granted
     *
     * @param string $resource
     * @param string $type
     * @return boolean
     */
    public function isGranted ($resource, $type = 'block')
    {
        $permissionsRoute = $this->rules['route'];
        $permissionsBlock = $this->rules['block'];
        ksort($permissionsRoute);
        ksort($permissionsBlock);
        $siteId = null;
        if (null !== $this->routeMatch() && $this->routeMatch()->getParam('site_id') != null) {
            $siteId = $this->routeMatch()->getParam('site_id');
        }

        try {
            // @todo retrieve the default permission grant
            switch ($type) {
                case 'route':
                    $granted = false;
                    $routeName = $resource;
                    $permissions = isset($permissionsRoute['route']) && is_array($permissionsRoute['route']) ? $permissionsRoute['route'] : [];
                    foreach ($permissions as $route => $permission) {
                        if (fnmatch($route, $routeName, FNM_CASEFOLD)) {
                            $permission = $permissions[$route];
                            // is the current role granted access?
                            $granted = $permission === Authorization::ALLOW ? true : false;
                        }
                    }
                    break;

                default:
                case 'block':
                    $granted = true;
                    $templateName = $resource;
                    $permissions = isset($permissionsBlock['block']) && is_array($permissionsBlock['block']) ? $permissionsBlock['block'] : [];
                    /* ensure that 'empty' site_id keys are last in the array -
                     * - we will always look for site-specific settings first
                     * if we match site-specific it will override any global defaults -> so we then break from the foreach
                     */
                    krsort($permissions);
                    foreach ($permissions as $site => $arr) {
                        foreach ($arr as $template => $permission) {
                            if (fnmatch($template, $templateName, FNM_CASEFOLD)) {
                                $setSite = (($site == $siteId) ? $siteId : '');
                                $permission = $permissions[$setSite][$template];
                                // is the current role granted access?
                                $granted = $permission === Authorization::ALLOW ? true : false;
                            }
                        }
                        /* After the internal iteration - if the 'current site' ($siteId) and the 'records site' ($site) match and the 'templateName' (current block being tested)
                         *  matches the one in the current record (array key of $arr) 'template' we can assume that the 'granted' status has been set accordingly
                         */
                        if ($site == $siteId && array_key_exists($templateName, $arr)) {
                            break;
                        }
                    }
                    break;
            }

        } catch(\Exception $e) {
            throw $e;
        }

        return $granted;
    }
}
