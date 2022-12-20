<?php

namespace Notification\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ManageController extends AbstractActionController
{
    /**
     * Display ALL notifications by site
     */
    public function directoryAction ()
    {
        $siteId = $this->params('site_id');
        $service = $this->notificationService();
        $notifications = $service->findAllBySiteId($siteId);

        return [
            'notifications' => $notifications
        ];
    }

    /**
     * Create ONE notification
     */
    public function createAction ()
    {
        $siteId = $this->params('site_id');
        $request  = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Notification\Form\Create');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // if sender data is not given, use the current user's data
                if (!(array_key_exists('sender_name', $data) && $data['sender_name']) && !(array_key_exists('sender_email', $data) && $data['sender_email'])) {
                    $learner = $this->identity();
                    $data['sender_name'] = $learner['name'];
                    $data['sender_email'] = $learner['email'];
                }

                // save the notification in the repository
                $service = $this->notificationService();
                $notification = $service->createNotification($data, $siteId);
                $notificationId = $notification['notification_id'];

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

                // send notification to learners
                $learnerIds = isset($data['learner_id']) ? $data['learner_id'] : [];
                $service->saveLearnerNotification($learnerIds, $notificationId);

                // send notification to groups
                $groupIds = isset($data['group_id']) ? $data['group_id'] : [];
                $service->saveGroupNotification($groupIds, $notificationId);

                // success
                $message = sprintf($this->translate('Successfully created the notification "%s".'), $notification['subject']);
                $cookie = new SetCookie('updateMessage', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('notification/manage/update', ['notification_id' => $notification['notification_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create new notification. An internal error has occurred. Please contact your support administrator for further help.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form' => $form,
            'message' => $message
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
     * Update ONE notification
     */
    public function updateAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);
        $request  = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Notification\Form\Update');

        // if a success meesage is passed
        $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save the notification in the repository
                $service = $this->notificationService();
                $notification = $service->updateNotification($data);

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

                // send notification to learners
                $learnerIds = isset($data['learner_id']) ? $data['learner_id'] : [];
                $service->saveLearnerNotification($learnerIds, $notificationId);

                // send notification to groups
                $groupIds = isset($data['group_id']) ? $data['group_id'] : [];
                $service->saveGroupNotification($groupIds, $notificationId);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the notification "%s".'), $notification['subject']);
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
                $message['error'] = sprintf($this->translate('Cannot update the notification "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $notification['subject']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->populateValues(Stdlib\ObjectUtils::extract($notification));

        return [
            'form' => $form,
            'notification' => $notification,
            'message' => $message
        ];
    }

    /**
     * Activate ONE notification
     */
    public function activateAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // activate notification
                $service->activateNotification($notificationId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully activated the notification "%s".'),$notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot activate the notification "%s"'), $notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
        }

        return [
            'notification' => $notification
        ];
    }

    /**
     * Deactivate ONE notification
     */
    public function deactivateAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // deactivate notification
                $service->deactivateNotification($notificationId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deactivated the notification "%s".'),$notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot deactivate the notification "%s"'), $notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
        }

        return [
            'notification' => $notification
        ];
    }

    /**
     * Delete ONE notification
     */
    public function deleteAction ()
    {
        $notificationId = $this->params('notification_id');
        $service = $this->notificationService();
        $notification = $service->findOneByNotificationId($notificationId);

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete notification
                $service->deleteNotification($notificationId);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the notification "%s".'),$notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot delete the notification "%s"'), $notification['subject']));
                return $this->redirect()->toRoute('notification/manage/directory');
            }
        }

        return [
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