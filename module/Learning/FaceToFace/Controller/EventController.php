<?php

namespace FaceToFace\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class EventController extends AbstractActionController
{

    /**
     * Display ALL events by activity
     */
    public function directoryAction ()
    {
        $activityId = $this->params('activity_id');
        $service    = $this->learningService();
        $activity   = $service->findOneLearningActivityById($activityId);
        $events     = $activity['events'];
        $message    = false;

        // if a success message is passed
        $directoryMessageSuccess = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageSuccess)) {
            $directoryMessageSuccess = $this->getRequest()->getCookie()->directoryMessageSuccess;
        }
        if ($directoryMessageSuccess) {
            $message['success'] = $this->translate($directoryMessageSuccess);
        }
        // if a success message is passed
        $directoryMessageError = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageError)) {
            $directoryMessageError = $this->getRequest()->getCookie()->directoryMessageError;
        }
        if ($directoryMessageError) {
            $message['error'] = $this->translate($directoryMessageError);
        }

        return [
            'activity' => $activity,
            'events'   => $events,
            'message'  => $message
        ];
    }

    /**
     * Create ONE event for this learning activity
     */
    public function createAction ()
    {
        $activityId = $this->params('activity_id');
        $siteId   = $this->params('site_id');
		$service  = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Event\Form\Create');
        $form->get('activity_id')->setValue($activityId);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                $data = $form->validate($post);
                $data['activity'] = $post['activity_id'];
                $data['site'] = $data['siteId'] = $siteId;

                // save event data
                $service = $this->eventService();
                $event = $service->createEvent($data);

                // success
                $message = $this->translate('The event was created successfully.');
                $cookie = new SetCookie('updateMessage', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/face-to-face/event/update', ['event_id' => $event['event_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create event. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form'     => $form,
            'activity' => $activity,
            'message'  => $message
        ];
    }

    /**
     * Display ONE event
     */
    public function readAction ()
    {
        $eventId = $this->params('event_id');
        $service = $this->eventService();
        $event = $service->findOneEventById($eventId);
        $activity = $event['activity'];

        return [
            'event' => $event
        ];
    }

    /**
     * Update ONE event
     */
    public function updateAction ()
    {
        $eventId  = $this->params('event_id');
        $service  = $this->eventService();
        $event    = $service->findOneEventById($eventId);
        $activity = $event['activity'];
        $request  = $this->getRequest();
        $message  = false;

        // if a success message is passed
        $updateMessage = false;
        if (isset($this->getRequest()->getCookie()->updateMessage)) {
            $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        }
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        // form
        $form = $this->form('Event\Form\Update');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                $data = $form->validate($post);

                // save in the  repository
                $event = $service->updateEvent($data);

                // success
                $message['success'] = $this->translate('The event was updated successfully.');
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
                $message['error'] = $this->translate('Cannot update event. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($event);

        return [
            'form'     => $form,
            'activity' => $activity,
            'event'    => $event,
            'message'  => $message
        ];
    }

    /**
     * Duplicate ONE event
     */
    public function duplicateAction ()
    {
        $eventId  = $this->params('event_id');
        $service  = $this->eventService();
        $event    = $service->findOneEventById($eventId);
        $activity = $event['activity'];
        $request  = $this->getRequest();

        // process form request
        if ($this->params('confirm') === 'yes') {
            // duplicate event
            $event = $service->duplicateEvent($eventId);

            // success
            $message = $this->translate('Created a copy of the event successfully.');
    //        $cookie = new SetCookie('updateMessage', $message, time() + 60 * 1, '/');
    //        $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addSuccessMessage($message);
            }
            return $this->redirect()->toRoute('learning/face-to-face/event/update', ['event_id' => $event['event_id']]);
        }

        return [
        	'activity' => $activity,
            'event' => $event
        ];
    }

    /**
     * Delete ONE event
     */
    public function deleteAction ()
    {
        $eventId  = $this->params('event_id');
        $service  = $this->eventService();
        $event    = $service->findOneEventById($eventId);
        $activity = $event['activity'];
        $request  = $this->getRequest();

        // process form request
        if ($this->params('confirm') === 'yes') {
            // duplicate event
            $event = $service->deleteEvent($eventId);

            // success
            $message = $this->translate('Deleted the event successfully.');
      //      $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
      //      $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addSuccessMessage($message);
            }
            return $this->redirect()->toRoute('learning/face-to-face/event/directory', ['activity_id' => $activity['activity_id']]);
        }

        return [
        	'activity' => $activity,
            'event' => $event
        ];
    }

    /**
     *
     * @return \FaceToFace\Service\FaceToFaceService
     */
    protected function learningService ()
    {
        return $this->service('FaceToFace\Service');
    }

    /**
     * Get the event service
     *
     * @return \Event\Service\EventService
     */
    public function eventService ()
    {
        return $this->service('Event\Service');
    }
}