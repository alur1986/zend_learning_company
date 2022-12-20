<?php

namespace Learning\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ManageController extends AbstractActionController
{
    /**
     * Display ALL learning activities
     */
    public function directoryAction ()
    {
        $siteId = $this->params('site_id');
        $groupType = $this->params('group_type');

        $service = $this->learningActivityService();
        $activities = $service->findAllLearningActivitiesBySiteId($siteId, $groupType);

        if (count($activities) == 0) {
            // no activities - send to 'add new' page with a message
            $this->flashMessenger()->addSuccessMessage($this->translate('There are currently no Activities available for the directory view - choose an activity type to begin creating one'));
            return $this->redirect()->toRoute('learning/manage/tools');
        }

        $message = false;
        // if a success message is passed
        $directoryMessageSuccess = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageSuccess)) {
            $directoryMessageSuccess = $this->getRequest()->getCookie()->directoryMessageSuccess;
        }
        if ($directoryMessageSuccess) {
            $message['success'] = $this->translate($directoryMessageSuccess);
        }
        // if an error is passed
        $directoryMessageError = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageError)) {
            $directoryMessageError = $this->getRequest()->getCookie()->directoryMessageError;
        }
        if ($directoryMessageError) {
            $message['error'] = $this->translate($directoryMessageError);
        }

        return [
            'activities' => $activities,
            'message'    => $message
        ];
    }

    /**
     * Display the tools page
     */
    public function toolsAction ()
    {
        return [];
    }

    /**
     * Get the Learning Activity service
     *
     * @return \Learning\Service\LearningService
     */
    protected function learningActivityService ()
    {
        return $this->service('Learning\Service');
    }
}