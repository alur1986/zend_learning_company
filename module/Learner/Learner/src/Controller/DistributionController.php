<?php

namespace Learner\Controller;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class DistributionController extends AbstractActionController
{

    /**
     * Manage learner generic settings
     */
    public function DirectoryAction ()
    {
        $siteId = $this->params('site_id');
        $userId = $this->params('user_id');
        $request  = $this->getRequest();

        /* @var $learnerService \Learner\Service\LearnerService */
        $learnerService = $this->service('Learner\Service');
        $learner = $learnerService->findOneByUserId($userId);
        /* @var $distributionService \Distribution\Learning\Service\LearningDistributionService */
        $distributionService = $this->service('Distribution\Learning');

        // when no message is set
        $message = false;

        // form
        $form    = false;
        $form = $this->form('Learner\Form\Distribution');
        $activities = $distributionService->findAllActivitiesByActivityType('scorm12', $siteId);
        $resources = $distributionService->findAllActivitiesByActivityType('resource', $siteId);
        $arr = array();
        foreach($activities as $row) {
            $key = $row['id'];
            $value = $row['title'];
            $arr[$key] = $value;
        }
        foreach($resources as $row) {
            $key = $row['id'];
            $value = $row['title'];
            $arr[$key] = $value;
        }
        // sort the array
        asort($arr);
        $form->get('activity_id')->setValueOptions($arr); // populate the Activity Select

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save learner distribution
                $learnerIds       = $data['user_id'];
                $activityIds      = $data['activity_id'];
                $distributionDate = $data['distribution_date'];
                $expiryDate       = $data['expiry_date'];

                // distribute
                $distributionService->distributeByLearners(array($learnerIds), array($activityIds), $distributionDate, $expiryDate);

                // success!
                $message['success'] = sprintf($this->translate("%s's selected activity was successfully distributed."), $learner['name']);
                if (!$request->isXmlHttpRequest() || $reload == 1) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation failure
            }
            catch (\Exception $e) {
                // fail!
                $message['error'] = $this->translate('Cannot update the learner distribution details. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        /* @var $service  */
        $service = $this->myLearningService();
        $activities = $service->findAllScormActivitiesBySiteId($siteId,$userId, ['scorm12', 'resource']);

        return [
            'message'    => $message,
            'activities' => $activities,
            'learner'    => $learner,
            'form'       => $form
        ];
    }

    public function myLearningService () {
        return $this->service('MyLearning\Service');
    }
}
