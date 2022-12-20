<?php

namespace Notification\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class LearnerController extends AbstractActionController
{

    /**
     * Display ALL active notifications for the learner
     */
    public function directoryAction ()
    {
        $notifications = $this->service('Notifications\Learner');

        return [
            'notifications' => $notifications
        ];
    }

    /**
     * Display ONE notification
     */
    public function readAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        return [
            'notification' => $notification
        ];
    }

    /**
     * Select learners and send ONE notification
     */
    public function sendAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // form
        $form = $this->form('Notification\Form\Learner');
        $form->populateValues($notification);

        // process form request
        if ($post = $this->post(true)) {
            try {
                // validate form
                $data = $form->validate($post);
                $learnerIds = isset($data['learner_id']) ? $data['learner_id'] : [];

                // update notification with the new learners
                $service->saveLearnerNotification($learnerIds, $notificationId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully sent the notification "%s" to learners'), $notification['subject']));
                return $this->redirect()->refresh();
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot send the notification "%s" to the learners. An internal error has occurred. Please contact your support administrator or try again later.'), $notification['subject']));
                return $this->redirect()->refresh();
            }
        }

        return [
            'form' => $form,
            'notification' => $notification
        ];
    }

    /**
     * Get the notification service provider
     *
     * @return \Notification\Service\NotificationService
     */
    public function notificationService ()
    {
        return $this->service('Notification\Service');
    }
}