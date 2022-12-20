<?php

namespace Webinar\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ManageController extends AbstractActionController
{

    /**
     * Display ALL webinar learning activities
     */
    public function directoryAction ()
    {
        $siteId = $this->params('site_id');
        $activityType = 'webinar';
        $service = $this->learningService();
        $activities = $service->findAllLearningActivitiesByType($activityType, $siteId);

        return [
            'activities' => $activities
        ];
    }

    /**
     * Create ONE webinar learning activity
     */
    public function createAction ()
    {
        $siteId  = $this->params('site_id');
        // form
        $form    = $this->form('Webinar\Form\Create');
        $request =  $this->getRequest();
        $message = false;
        $service = $this->learningService();
        $plans   = $service->getAllLearningPlansBySiteId($siteId);
        $arr = array();
        foreach ($plans as $plan) {
            $arr[] = array_merge([
                'label' => $plan['title'],
                'value' => $plan['plan_id']
            ], Stdlib\ObjectUtils::extract($plan));
        }
        $form->get('plan_id')->setValueOptions($arr);

        // process form request
        if ($post = $this->post(false)) {
            try {
                /* This is a secondary part of a process initialised in the form-factory - it doesn't seem possible to 'empty' the checkbox value in the factory */
                if (isset($post['auto_distribute']) && ($post['auto_distribute'] == 'on' || $post['auto_distribute'] == true)) {
                    // if auto_distribute is set to ON
                    if (($post['auto_distribute_on_registration'] == 0 || $post['auto_distribute_on_registration'] == false) && ($post['auto_distribute_on_login'] == 0 || $post['auto_distribute_on_login'] == false)) {
                        // if neither auto_distribute_on_registration or auto_distribute_on_login are set to on, force the auto_distribute to fail
                        $post['auto_distribute'] = null;
                    }
                }
                // form validation
                $data = $form->validate($post);

                // save in repository
                $service = $this->learningService();
                $activity = $service->createActivity($data);

                // success
                $message = sprintf($this->translate('The learning activity %s has been created successfully.'), $activity['title']);
                $cookie = new SetCookie('updateMessage', $message, time() + 30, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute(sprintf('learning/%s/update', $activity['activity_type']), ['activity_id' => $activity['activity_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create the new learning activity. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

         return [
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Display ONE webinar learning activity
     */
    public function readAction ()
    {
        $activityId = $this->params('activity_id');
        $service = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);

        return [
            'activity' => $activity
        ];
    }

    /**
     * Display ALL events in ONE webinar learning activity
     */
    public function eventsAction ()
    {
        $activityId = $this->params('activity_id');
        $service = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $events = $activity['events'];

        return [
            'activity' => $activity,
            'events' => $events
        ];
    }

    /**
     * Update ONE webinar learning activity
     */
    public function updateAction ()
    {
        $siteId     = $this->params('site_id');
        $activityId = $this->params('activity_id');
        $service    = $this->learningService();
        $activity   = $service->findOneLearningActivityById($activityId);
        $request    = $this->getRequest();
        $message    = false;

        // if a success message is passed
        $updateMessage = false;
        if (isset($this->getRequest()->getCookie()->updateMessage)) {
            $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        }
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        // get the plan_id's any all/any learning plans this activity is included in
        $planIds = array();
        $hasPlans = $activity['hasPlans'];
        foreach ($hasPlans as $hasPlan) {
            $planIds[] = $hasPlan['plans']['plan_id'];
        }

        $plans   = $service->getAllLearningPlansBySiteId($siteId);
        $arr     = array();
        foreach ($plans as $plan) {
            $selected = false;
            if (in_array($plan['plan_id'], $planIds)) { $selected = 'selected'; }
            $arr[] = array_merge([
                'label' => $plan['title'],
                'value' => $plan['plan_id'],
                'selected' => $selected
            ], Stdlib\ObjectUtils::extract($plan));
        }

        // form
        $form = $this->form('Webinar\Form\Update');
        $form->get('plan_id')->setValueOptions($arr);

        // process form request
        if ($post = $this->post(false)) {
            try {
                /* This is a secondary part of a process initialised in the form-factory - it doesn't seem possible to 'empty' the checkbox value in the factory */
                if (isset($post['auto_distribute']) && ($post['auto_distribute'] == 'on' || $post['auto_distribute'] == true)) {
                    // if auto_distribute is set to ON
                    if (($post['auto_distribute_on_registration'] == 0 || $post['auto_distribute_on_registration'] == false) && ($post['auto_distribute_on_login'] == 0 || $post['auto_distribute_on_login'] == false)) {
                        // if neither auto_distribute_on_registration or auto_distribute_on_login are set to on, force the auto_distribute to fail
                        $post['auto_distribute'] = null;
                    }
                }
                // form validation
                $data = $form->validate($post);

                // save in repository
                $activity = $service->updateActivity($data);

                // success
                $message['success'] = $this->translate('The learning activity has been updated successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = sprintf($this->translate('Cannot update the learning activity "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $activity['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($activity);

        return [
            'form'     => $form,
            'activity' => $activity,
            'message'  => $message
        ];
    }

    /**
     * Duplicate ONE webinar learning activity
     */
    public function duplicateAction ()
    {
        $activityId = $this->params('activity_id');
        $service  = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the venue
                $activity = $service->duplicateActivity($activityId);

                // success
                $message = $this->translate('The learning activity has been successfully duplicated.');
     //           $cookie = new SetCookie('updateMessage', $message, time() + 60 * 1, '/');
     //           $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute(sprintf('learning/%s/update', $activity['activity_type']), ['activity_id' => $activity['activity_id']]);
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot duplicate the learning activity. An internal error has occurred. Please contact your support administrator or try again later.');
      //          $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
       //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
        }

        return [
            'activity' => $activity
        ];
    }

    /**
     * Delete ONE webinar learning activity
     */
    public function deleteAction ()
    {
        $activityId = $this->params('activity_id');
        $service  = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the venue
                $service->deleteActivity($activityId);

                // success
                $message = $this->translate('The learning activity has been deleted successfully.');
      //          $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
      //          $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot delete the learning activity. An internal error has occurred. Please contact your support administrator or try again later.');
      //          $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
      //          $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
        }

        return [
            'activity' => $activity
        ];
    }

    /**
     * Activate ONE webinar learning activity
     */
    public function activateAction ()
    {
        $activityId = $this->params('activity_id');
        $service  = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the venue
                $service->activateActivity($activityId);

                // success
                $message = $this->translate('The learning activity has been activated successfully.');
        //        $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
        //        $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($this->translate('The learning activity has been activated successfully.'));
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot activate the learning activity. An internal error has occurred. Please contact your support administrator or try again later.');
      //          $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
      //          $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
        }

        return [
            'activity' => $activity
        ];
    }

    /**
     * Deactivate ONE webinar learning activity
     */
    public function deactivateAction ()
    {
        $activityId = $this->params('activity_id');
        $service  = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the venue
                $service->deactivateActivity($activityId);

                // success
                $message = $this->translate('The learning activity has been deactivated successfully.');
      //          $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
       //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot deactivate the learning activity. An internal error has occurred. Please contact your support administrator or try again later.');
       //         $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
        //        $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/manage/directory');
            }
        }

        return [
            'activity' => $activity
        ];
    }

    /**
     * Get the Webinar doctrine service provider
     *
     * @return \Webinar\Service\WebinarService
     */
    protected function learningService ()
    {
        return $this->service('Webinar\Service');
    }
}