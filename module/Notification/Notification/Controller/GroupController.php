<?php

namespace Notification\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class GroupController extends AbstractActionController
{

    /**
     * Select groups and send ONE notification
     */
    public function sendAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // form
        $form = $this->form('Notification\Form\Group');
        $form->populateValues($notification);

        // process form request
        if ($post = $this->post(true)) {
            try {
                // validate form
                $data = $form->validate($post);
                $groupIds = isset($data['group_id']) ? $data['group_id'] : [];

                // save groups in the  repository
                $service->saveGroupNotification($groupIds, $notificationId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully sent the notification "%s" to the groups'), $notification['subject']));
                return $this->redirect()->refresh();
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot send the notification "%s" to the groups. An internal error has occurred. Please contact your support administrator or try again later.'), $notification['subject']));
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