<?php

namespace Authorization\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

/**
 * Display roles available in the Access Control List
 *
 * Authorization\Controller\RoleController
 *
 * @category Authorization
 * @package Authorization
 * @subpackage Controller
 * @copyright Copyright (c) 2015 Savv-e Pty Ltd
 */
class RoleController extends AbstractActionController
{

    /**
     * Display ALL roles
     */
    public function directoryAction ()
    {
        $roles = $this->service('Authorization\Roles\All');

        return [
            'roles' => $roles
        ];
    }

    /**
     * Create ONE new role
     */
    public function createAction ()
    {
        $form = $this->form('Authorization\Role\Form\Create');

        // proces form request
        if ($post = $this->post(false)) {
            try {
                // validate
                $data = $form->validate($post);

                // save
                $service = $this->authorizationService();
                $role = $service->createRole($data);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully created the role "%s"', $role['title']));
                return $this->redirect()->toRoute('secure/role/update', ['role_id' => $role['id']]);
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot create the role.'));
                return $this->redirect()->toRoute('secure/role/directory');
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * View ONE role
     */
    public function readAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);
        $learners = $this->service('Authorization\Role\Learners');
        $rules = $this->service('Authorization\Role\Rules');

        return [
            'role' => $role,
            'learners' => $learners,
            'rules' => $rules
        ];
    }

    /**
     * Update ONE role
     */
    public function updateAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        // form
        $form = $this->form('Authorization\Role\Form\Update');
        $form->populateValues($role);

        // proces form request
        if ($post = $this->post(false)) {
            try {
                // validate
                $data = $form->validate($post);

                // save
                $service = $this->authorizationService();
                $role = $service->updateRole($data);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully created the role "%s"', $role['title']));
                return $this->redirect()->toRoute('secure/role/update', ['role_id' => $role['id']]);
            }
            catch (\Exception $e) {
                // failed
                throw $e;
            }
        }

        return [
            'role' => $role,
            'form' => $form
        ];
    }

    /**
     * Delete ONE role
     */
    public function deleteAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        // process form request
        if ($this->params('confirm') == 'yes') {
            try {
                // delete role
                $service->deleteRole($roleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully deleted the role "%s"', $role['title']));
                return $this->redirect()->toRoute('secure/role/directory');
            }
            catch (\Exception $e) {
                // failed
                throw $e;
            }
        }

        return [
            'role' => $role
        ];
    }

    /**
     * Activate ONE role
     */
    public function activateAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        // process form request
        if ($this->params('confirm') == 'yes') {
            try {
                // delete role
                $service->activateRole($roleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully activate the role "%s"', $role['title']));
                return $this->redirect()->toRoute('secure/role/directory');
            }
            catch (\Exception $e) {
                // failed
                throw $e;
            }
        }

        return [
            'role' => $role
        ];
    }

    /**
     * Deactivate ONE role
     */
    public function deactivateAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        // process form request
        if ($this->params('confirm') == 'yes') {
            try {
                // delete role
                $service->deactivateRole($roleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully deactivated the role "%s"', $role['title']));
                return $this->redirect()->toRoute('secure/role/directory');
            }
            catch (\Exception $e) {
                // failed
                throw $e;
            }
        }

        return [
            'role' => $role
        ];
    }

    /**
     * Get the Authorisation service provided
     *
     * @return \Authorization\Service\AuthorizationService
     */
    protected function authorizationService ()
    {
        $service = $this->service('Zend\Authorization\AuthorizationService');
        return $service;
    }
}