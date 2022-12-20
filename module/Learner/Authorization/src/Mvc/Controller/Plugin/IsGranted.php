<?php

namespace Authorization\Mvc\Controller\Plugin;

use Savve\Mvc\Controller\Plugin\AbstractPlugin;

class IsGranted extends AbstractPlugin
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
    public function __invoke ($resource, $type = 'route')
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
        $permissions = $this->rules;
        ksort($permissions);

        // @todo retrieve the default permission grant
        switch ($type) {
            case 'route':
                $granted = false;
                $routeName = $resource;
                $permissions = isset($permissions['route']) && is_array($permissions['route']) ? $permissions['route'] : [];
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
                $permissions = isset($permissions['block']) && is_array($permissions['block']) ? $permissions['block'] : [];
                foreach ($permissions as $template => $permission) {
                    if (fnmatch($template, $templateName, FNM_CASEFOLD)) {
                        $permission = $permissions[$template];

                        // is the current role granted access?
                        $granted = $permission === Authorization::ALLOW ? true : false;
                    }
                }
                break;
        }

        return $granted;
    }
}
