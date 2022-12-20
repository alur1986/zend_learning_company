<?php

namespace Learner\Controller;

use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Savvecentral\Entity;
use Savve\Stdlib;

class EmploymentController extends AbstractActionController
{

    /**
     * Manage learner employment details
     */
    public function manageAction ()
    {
        $userId = $this->params('user_id');
        $siteId = $this->params('site_id');
        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $learner = $service->findOneByUserId($userId);
        $request = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Learner\Form\Employment');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save employment details
                $service->saveEmployment($data);

                // success!
                $message['success'] = sprintf($this->translate("%s's employment details successfully updated."), $learner['name']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
                $learner = $service->findOneByUserId($userId);
                $employment = $learner['employment'];
            }
            catch (\Exception\InvalidFormException $e) {
                // form validation, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot update employment details. Please try again later. ' . $e->getMessage());
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }

        } else {
            $employment = $learner['employment'] ?  : false;
            if ($employment == false) {
                $employment = new Entity\Employment();
                $employment['user_id'] = $userId;
            }
        }
        $sd = $employment['start_date'];
        if ($sd) $employment['start_date'] = $sd->format('Y-m-d');
        $ed = $employment['end_date'];
        if ($ed) $employment['end_date'] = $ed->format("Y-m-d");
    //    $form->populateValues(Stdlib\ObjectUtils::extract($employment), true);
        $form->bind($employment);

        return [
            'message'     => $message,
            'learner'     => $learner,
            'employment'  => $employment,
            'form'        => $form
        ];
    }
}