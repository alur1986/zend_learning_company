<?php

namespace LearningPlan\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ManageController extends AbstractActionController
{

    /**
     * Display ALL Learning Plans
     */
    public function directoryAction ()
    {
        $siteId = $this->params('site_id');
        $service =$this->learningPlanService();

        $plans = $service->findAllBySiteId($siteId);

        return [
            'plans' => $plans
        ];
    }

    /**
     * Create ONE Learning Plan
     */
    public function createAction ()
    {
        // form
        $form    = $this->form('LearningPlan\Form\Create');
        $request = $this->getRequest();
        $message = false;

        if ($post = $this->post(false)) {
            try {

                // form validation
                $data = $form->validate($post);

                // save in repository
                $service  = $this->learningPlanService();
                $plan     = $service->createPlan($data);

                // success
                $message['success'] = sprintf($this->translate('The Learning Playlist %s has been created successfully. You may now add Learning Activities to this playlist'), $plan['title']);
                $cookie = new SetCookie('updateMessage', $message['success'], time() + 30, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/activities', ['plan_id' => $plan['plan_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create the new Learning Playlist. An internal error has occurred. Please contact your support administrator or try again later.');
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
     * Display ONE Learning Plan
     */
    public function readAction ()
    {
        $planId = $this->params('plan_id');
        $service = $this->learningPlanService();
        $plan = $service->findOneLearningPlanById($planId);

        return [
            'plan' => $plan
        ];
    }

    /**
     * Update ONE Learning Plan
     */
    public function updateAction ()
    {
    //    $siteId  = $this->params('site_id');
        $planId  = $this->params('plan_id');
        $service = $this->learningPlanService();
        $plan    = $service->findOneLearningPlanById($planId);
        $request = $this->getRequest();
        $message = false;

        // if a success message is passed
        $updateMessage = false;
        if (isset($this->getRequest()->getCookie()->updateMessage) ) {
            $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        }
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        // form
        $form = $this->form('LearningPlan\Form\Update');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // form validation
                $data = $form->validate($post);

                // save in repository
                $plan = $service->updatePlan($data);

                // success
                $message['success'] = $this->translate('The Learning Plan has been updated successfully.');
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
                $message['error'] = sprintf($this->translate('Cannot update the Learning Plan "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $plan['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->populateValues(Stdlib\ObjectUtils::extract($plan), true);
       // $form->bind($plan);

        return [
            'form'    => $form,
            'plan'    => $plan,
            'message' => $message
        ];
    }

    public function viewAction()
    {
        $planId = $this->params('plan_id');
        $userId = $this->params('user_id');
        $service = $this->learningPlanService();
        $message = false;

        $plan = $service->findAllByPlanId($planId, $userId);

        return [
            'plan'    => $plan,
            'message' => $message
        ];
    }

    /**
     * Delete a Learning Plan
     *
     * @return array|\Zend\Http\Response
     */
    public function deleteAction ()
    {
        $planId = $this->params('plan_id');
        $service  = $this->learningPlanService();
        $plan = $service->findOneLearningPlanById($planId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the learning plan
                $service->deletePlan($planId);

                // success
                $message['success'] = $this->translate('The learning plan has been deleted successfully.');
                //         $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
                //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot delete the learning plan. An internal error has occurred. Please contact your support administrator or try again later.');
                //          $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
                //          $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
        }

        return [
            'plan' => $plan
        ];
    }

    /**
     * Activate ONE Learning Plan
     */
    public function activateAction ()
    {
        $planId = $this->params('plan_id');
        $service  = $this->learningPlanService();
        $plan = $service->findOneLearningPlanById($planId);
        $request  = $this->getRequest();
        $message = false;

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // activate the learning plan
                $plan = $service->activatePlan($planId);

                // success
                $message['success'] = $this->translate('The learning plan has been activated successfully.');
                //          $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
                //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot activate the learning plan. An internal error has occurred. Please contact your support administrator or try again later.');
                //         $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
                //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
        }

        return [
            'plan' => $plan,
            'message'  => $message
        ];
    }

    /**
     * Deactivate ONE Learning Plan
     */
    public function deactivateAction ()
    {
        $planId = $this->params('plan_id');
        $service  = $this->learningPlanService();
        $plan = $service->findOneLearningPlanById($planId);
        $request  = $this->getRequest();
        $message = false;

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // deactivate the learning plan
                $plqan = $service->deactivatePlan($planId);

                // success
                $message['success'] = $this->translate('The learning plan has been deactivated successfully.');
                //          $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
                //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot dedactivate the learning plan. An internal error has occurred. Please contact your support administrator or try again later.');
                //         $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
                //         $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                }
                return $this->redirect()->toRoute('learning/learning-plans/directory');
            }
        }

        return [
            'plan' => $plan,
            'message'  => $message
        ];
    }

    /**
     * Learning Plan Service
     *
     * @return \LearningPlan\Service\LearningPlan
     */
    public function learningPlanService ()
    {
        try {
            return $this->service('LearningPlan\Service');
        } catch (\Exception $e) {
            throw $e;
        }

    }
}