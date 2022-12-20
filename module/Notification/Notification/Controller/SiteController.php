<?php

namespace Notification\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class SiteController extends AbstractActionController
{

    /**
     * Display ALL active notifications for the learner
     */
    public function directoryAction ()
    {
        $notifications = $this->service('Notifications\Site');

        return [
            'notifications' => $notifications
        ];
    }

    /**
     * Send notification to all
     */
    public function sendAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // form
        $form = $this->form('Notification\Form\Site');
        $form->populateValues($notification);

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // send notification to site
                switch ($data['send_to_all']) {
                    case 'no':
                        $siteId = $this->params('site_id');
                        $service->deleteSiteNotification($siteId, $notificationId);
                        break;
                    case 'yes':
                        $siteId = $this->params('site_id');
                        $service->saveSiteNotification($siteId, $notificationId);
                        break;
                }

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully sent the notification "%s" to all'), $notification['subject']));
                return $this->redirect()->refresh();
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot send the notification "%s" to the all. An internal error has occurred. Please contact your support administrator or try again later.'), $notification['subject']));
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