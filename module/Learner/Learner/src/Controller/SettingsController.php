<?php

namespace Learner\Controller;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class SettingsController extends AbstractActionController
{

    /**
     * Manage learner generic settings
     */
    public function settingsAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $request = $this->getRequest();
        $message = false;
        $learner = $service->findOneByUserId($userId);
        $isAjax  = 0;
        $getLearner  = $this->getViewHelper( 'Learner' );
        $user        = $getLearner();
        $learnerId   = $user['userId'];
        if ($learnerId == $userId) {
            $reload = 1;
        } else {
            $reload = 0;
        }

        // form
        $form = $this->form('Learner\Form\Settings');
    //    $form->populateValues(Stdlib\ObjectUtils::extract($learner), true);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save learner
                $service->update($data);

                // success!
                $message['success'] = sprintf($this->translate("%s's details were successfully updated."), $learner['name']);
                if (!$request->isXmlHttpRequest() || $reload == 1) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                } else {
                    $isAjax = 1;
                }
                $learner = $service->findOneByUserId($userId);
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation failure
            }
            catch (\Exception $e) {
                // fail!
                $message['error'] = $this->translate('Cannot update the learner details. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                } else {
                    $isAjax = 1;
                }
            }
        }
        $form->populateValues(Stdlib\ObjectUtils::extract($learner), true);

        return [
            'message' => $message,
            'isAjax'  => $isAjax,
            'learner' => $learner,
            'form'    => $form
        ];
    }

    /**
     * Manage learner email settings
     */
    public function emailAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $learner = $service->findOneByUserId($userId);

        return [
            'learner' => $learner
        ];
    }
}