<?php

namespace Group\Controller;

use Savve\Stdlib\Exception;
use Savve\Controller\AbstractController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;// as GetCookie;
use Zend\Http\Request;

class ManageController extends AbstractController
{

    /**
     * Displays ALL the groups
     */
    public function directoryAction ()
    {
        /* @var $groups \Doctrine\Common\Collections\ArrayCollection */
     //   $groups = $this->service('Group\All');

    //    $request = $this->getRequest();
        $groups  = false;
        $allDirectoryAccess = false;

    /*    if (!$request->isXmlHttpRequest()) {

            $service = $this->groupService();
            $siteId  = $this->getParam('site_id');
            $userId  = $this->params('user_id'); // send to Service for admin privilege check
            $groups  = $service->findAllGroupDetailsBySiteId($siteId, $userId);

            //get the user's current role
            $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
            //Set to guest access if no user level is found
            $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;
            $allDirectoryAccess = $currentUserLevel >= 55555; //Company admin and above
        }*/

        return [
            'groups' => $groups,
            'allDirectoryAccess' =>  $allDirectoryAccess
        ];
    }

    /**
     * Display ONE group
     */
    public function readAction ()
    {
        $groupId = $this->params('group_id');

        $service = $this->groupService();
        $group = $service->findOneByGroupId($groupId);
        $learners = $group->getLearners();

        return [
            'group' => $group,
            'learners' => $learners
        ];
    }

    /**
     * Create ONE group
     */
    public function createAction ()
    {
        $request  = $this->getRequest();

        $siteId = $this->params('site_id');

        // form
        $form = $this->form('Group\Form\Create');

        $message = array();
        $isAPIResponse = false;

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // add group to repository
                $service = $this->groupService();
                $group = $service->createGroup($data, $siteId);

                // success
                $text = $this->translate('The group %s has been created successfully.');
                $message['success'] = sprintf($text, $group['name']);
                $cookie = new SetCookie('newGroup', $group['name'], time() + 30, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (isset($post['isAPIData']) && $post['isAPIData'] == 1) {
                    $isAPIResponse = 1;
                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['success']);
                        return $this->redirect()->refresh();
                    }
                }
           //     $this->flashMessenger()->addSuccessMessage($message['success']);
                return $this->redirect()->toRoute('group/update', ['group_id' => $group['group_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create group. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
     //           $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create group. An internal error has occurred. Please contact your support administrator or try again later.')));
     //           return $this->redirect()->refresh();
            }
        }

        return [
            'message' => $message,
            'form'    => $form
        ];
    }

    /**
     * Update ONE group
     */
    public function updateAction ()
    {
        $request  = $this->getRequest();

        $groupId = $this->params('group_id');

        // get the current group
        $service = $this->groupService();
        $group = $service->findOneByGroupId($groupId);

        // form
        $form = $this->form('Group\Form\Update');
        $form->bind($group);

        $newGroup = false;
        if (isset($this->getRequest()->getCookie()->newGroup)) {
            $newGroup = $this->getRequest()->getCookie()->newGroup;
        }
        $message = false;
        if ($newGroup) {
            $message['success'] = sprintf($this->translate("The group %s has been created successfully."), $newGroup);
            $cookie = new SetCookie('newGroup', false, time() - 120, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
        }
        $isAPIResponse = false;

        // process form submit
    //    if ($post = $this->post(true)) {
        if ($post = $_POST) {
            try {
                // validate form
                $group = $form->validate($post);

                // update in the repository
                $service->updateGroup($group);

                // success
                $text = $this->translate('The group %s has been updated successfully.');
                $message['success'] = sprintf($text, $group['name']);
                if (isset($post['isAPIData']) && $post['isAPIData'] == 1) {
                    $isAPIResponse = 1;
                    return [
                        'message'  => $message,
                        'isAPIResponse' => $isAPIResponse
                    ];
                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['success']);
                        $this->redirect()->refresh();
                    }
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validaton error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = sprintf($this->translate('Cannot update the group %s. An internal error has occurred. Please contact your support administrator or try again later.'), $group['name']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    $this->redirect()->refresh();
                }
                if (isset($post['isAPIData']) && $post['isAPIData'] == 1) {
                    $isAPIResponse = 1;
                    return [
                        'message'  => $message,
                        'isAPIResponse' => $isAPIResponse
                    ];
                }
      //          $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot update the group %s. An internal error has occurred. Please contact your support administrator or try again later.'), $group['name']));
            }
        }

        return [
            'message'  => $message,
            'group'    => $group,
            'form'     => $form,
            'isAPIResponse' => $isAPIResponse
        ];
    }

    /**
     * Delete ONE group
     */
    public function deleteAction ()
    {
        $groupId = $this->params('group_id');

        // get the current group
        $service = $this->groupService();
        $group = $service->findOneByGroupId($groupId);

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // remove from repository
                $service->deleteGroup($group);

                // success
                $this->flashMessenger()->addSuccessMessage($this->translate('The group has been deleted successfully.'));
                return $this->redirect()->toRoute('group/directory');
            }
            catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot activate the group "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $group['name']));
                $this->redirect()->toRoute('group/directory');
            }
        }

        return [
            'group' => $group
        ];
    }

    /**
     * Get the Doctrine service Group service
     *
     * @return \Group\Service\GroupService
     */
    public function groupService ()
    {
        return $this->service('Group\Service');
    }
}