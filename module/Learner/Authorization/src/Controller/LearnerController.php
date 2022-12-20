<?php

namespace Authorization\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

/**
 * Manage learners in roles
 *
 * Authorization\Controller\LearnerController
 *
 * @category Authorization
 * @package Authorization
 * @subpackage Controller
 * @copyright Copyright (c) 2015 Savv-e Pty Ltd
 */
class LearnerController extends AbstractActionController
{

    /**
     * Display ALL learners
     */
    public function directoryAction ()
    {
        $roleId = $this->params('role_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);
        $learners = $this->service('Authorization\Role\Learners');
        $request = $this->getRequest();
        $message = false;

        // process form request
        if ($post = $this->post(false)) {
            try {
                if (isset($post['learner_id']) && $post['learner_id']) {
                    $roleId = isset($post['role_id']) ? $post['role_id'] : $role['id'];
                	$learnerId = isset($post['learner_id']) ? $post['learner_id'] : [];

                    // remove learners
                    $service->removeLearnerFromRole($learnerId, $roleId);
                }

                // success
                $message['success'] = sprintf($this->translate('Successfully removed the learners from the role "%s".'), $role['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot remove learners from the role.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'role' => $role,
            'learners' => $learners,
            'message' => $message
        ];
    }

    /**
     * Add learners to the role
     */
    public function addAction ()
    {
        $roleId = $this->params('role_id');
        $siteId = $this->params('site_id');
        $service = $this->authorizationService();
        $role = $service->findOneRoleById($roleId);
        $request = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Authorization\Learner\Form\Learner');
        $form->get('role_id')->setValue($roleId);

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                $roleId = isset($data['role_id']) ? $data['role_id'] : $role['id'];
                $learnerId = isset($data['learner_id']) ? $data['learner_id'] : [];

                if (!$learnerId) {
                    $message['error'] = $this->translate('There are no learners selected to be added to the role.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addErrorMessage($message['error']);
                        return $this->redirect()->refresh();
                    }
                }

                // save in repository
                $success = $service->addLearnerToRole($learnerId, $roleId);

                // success
                $message['success'] = sprintf($this->translate('Successfully added learners to the role "%s".'), $role['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // validation error
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot add learners to the role.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $learners = $service->findAllLearnersInSite($siteId);

        // extract the learner IDs from the collection
        $learnerIds = $learners->map(function($item){ return $item['user_id']; });
        $learnerIds = Stdlib\ObjectUtils::toArray($learnerIds);
        $form->get('learner_id')->setValue($learnerIds);

        return [
            'role' => $role,
            'form' => $form,
            'message' => $message
        ];
    }

    /**
     * Display ONE learner
     */
    public function readAction ()
    {
        $learnerId = $this->params('user_id');
        $service = $this->authorizationService();
        $learner = $service->findOneLearnerById($learnerId);
        $roles = $service->findAllRolesByLearnerId($learnerId);

        return [
            'learner' => $learner,
            'roles' => $roles
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