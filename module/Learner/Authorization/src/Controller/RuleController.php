<?php

namespace Authorization\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

/**
 * Manage rules in roles
 *
 * Authorization\Controller\RuleController
 *
 * @category Authorization
 * @package Authorization
 * @subpackage Controller
 * @copyright Copyright (c) 2015 Savv-e Pty Ltd
 */
class RuleController extends AbstractActionController
{

    /**
     * Add rules to the role
     */
    public function addAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        // form
        $form = $this->form('Authorization\Rule\Form\Create');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the rule(s)
                $roleId = isset($data['role_id']) ? $data['role_id'] : [];
                $resourceId = isset($data['resource_id']) ? $data['resource_id'] : [];
                $permission = isset($data['permission']) ? $data['permission'] : 'allow';
                $siteId = isset($data['site_id']) ? $data['site_id'] : null;

                // save in repository
                $service = $this->authorizationService();
                $success = $service->createRule($roleId, $resourceId, $permission, $siteId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully created the rules'));
				return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot create rules for the role %s', $role['tite']));
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
        }

        return [
            'form' => $form,
            'role' => $role
        ];
    }

    /**
     * Display ALL rules for ONE role
     */
    public function readAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);

        return [
            'role' => $role
        ];
    }

    /**
     * Delete ONE rule
     */
    public function deleteAction ()
    {
        $ruleId = $this->params('rule_id');
        $service = $this->authorizationService();
        $rule = $service->findOneRuleById($ruleId);
        $role = $rule['role'];

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete rule
                $success = $service->deleteRule($ruleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successsfully delete the rule'));
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot delete the rule for the role %s', $role['title']));
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
        }

        return [
            'rule' => $rule
        ];
    }

    /**
     * Update ONE rule to have ALLOW permission
     */
    public function allowAction ()
    {
        $ruleId = $this->params('rule_id');
        $service = $this->authorizationService();
        $rule = $service->findOneRuleById($ruleId);
        $role = $rule['role'];

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // allow rule
                $rule = $service->allowRule($ruleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successsfully updated the rule'));
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
            catch (\Exception $e) {
                // failed
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
        }

        return [
            'rule' => $rule
        ];
    }

    /**
     * Update ONE rule to have DENY permission
     */
    public function denyAction ()
    {
        $ruleId = $this->params('rule_id');
        $service = $this->authorizationService();
        $rule = $service->findOneRuleById($ruleId);
        $role = $rule['role'];

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // allow rule
                $rule = $service->denyRule($ruleId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successsfully updated the rule'));
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
            catch (\Exception $e) {
                // failed
                return $this->redirect()->toRoute('secure/rule/read', ['role_id' => $role['id']]);
            }
        }

        return [
            'rule' => $rule
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