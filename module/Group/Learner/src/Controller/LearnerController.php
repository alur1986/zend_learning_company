<?php

namespace Group\Learner\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Controller\AbstractController;

class LearnerController extends AbstractController
{

    /**
     * Display ALL the groups of ONE learner
     */
    public function groupsAction ()
    {
        $userId  = $this->params('user_id');
        $service = $this->groupLearnerService();
        $request = $this->getRequest();
        $message = false;
    //    $learner = $service->findOneLearnerById($userId);
    //    $groups = $learner['groups'];

        $form = $this->form('Group\Learner\Form\AddLearnerToGroup');
        $form->get('learner_id')
            ->setValue($userId);

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // add learner to the group
                $learnerIds = (array) isset($data['learner_id']) ? $data['learner_id'] : $userId;
                $groupId = (array) $data['group_id'];
                $service->addLearnersToGroups((array) $learnerIds, (array) $groupId);

                // success
                $message['success'] = $this->translate("Learners have been added to the group successfully.");
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate("Cannot add learner to the group. " . $e->getMessage());
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $learner = $service->findOneLearnerById($userId);
        $groups = $learner['groups'];

        return [
            'message' => $message,
            'groups'  => $groups,
            'learner' => $learner,
            'form'    => $form
        ];
    }

    /**
     * Get the group learners service provider
     *
     * @return \Group\Learner\Service\GroupLearnerService
     */
    protected function groupLearnerService ()
    {
        return $this->service('Group\Learner\Service');
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