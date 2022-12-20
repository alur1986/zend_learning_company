<?php

namespace Report\EventProgressDetails\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class FilterController extends AbstractActionController
{

    /**
     * Display ALL filters
     */
    public function directoryAction ()
    {
        $siteId = $this->params('site_id');
        $service = $this->reportFilterService();
        $filters = $service->findAllFiltersBySiteId($siteId, 'report-event-progress-details');

        $message = false;
        // if a filter has been deleted
        $deleteEPDFilter = false;
        if (isset($this->getRequest()->getCookie()->deleteEPDFilter)) {
            $deleteEPDFilter = $this->getRequest()->getCookie()->deleteEPDFilter;
        }
        if ($deleteEPDFilter) {
            $message['success'] = sprintf($this->translate('Successfully deleted the filter "%s"'), $deleteEPDFilter);
        }
        // if a filter has NOT been deleted
        $deleteEPDFilterError = false;
        if (isset($this->getRequest()->getCookie()->deleteEPDFilterError)) {
            $deleteEPDFilterError = $this->getRequest()->getCookie()->deleteEPDFilterError;
        }
        if ($deleteEPDFilterError == 1) {
            $message['error'] = $this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }
        // if a filter has NOT been executed
        $executeEPDFilterError = false;
        if (isset($this->getRequest()->getCookie()->executeEPDFilterError)) {
            $executeEPDFilterError = $this->getRequest()->getCookie()->executeEPDFilterError;
        }
        if ($executeEPDFilterError == 1) {
            $message['error'] = $this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        return [
            'filters' => $filters,
            'message' => $message
        ];
    }

    /**
     * Create a new filter
     */
    public function createAction ()
    {
        $sessionId = $this->params('session_id');
        $siteId    = $this->params('site_id');
        $service   = $this->reportFilterService();
        $request   = $this->getRequest();
        $message   = false;

        $session   = $this->session($sessionId);
        $filter    = $session->getArrayCopy();

        // form
        $form = $this->form('Report\EventProgressDetails\Form\FilterCreate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // extract data from the form values
                $data = Stdlib\ObjectUtils::extract($data);
                Stdlib\ArrayUtils::arrayPush($data, $filter);
                $filterData = [
                    'site_id' => $siteId,
                    'activity_id' => array_key_exists('activity_id', $data) ? (array) $data['activity_id'] : null,
                    'event_id' => array_key_exists('event_id', $data) ? (array) $data['event_id'] : null,
                    'group_id' => array_key_exists('group_id', $data) ? (array) $data['group_id'] : null,
                    'learner_id' => array_key_exists('learner_id', $data) ? (array) $data['learner_id'] : null,
                    'show_from' => array_key_exists('show_from', $data) && $data['show_from'] ? (new \DateTime($data['show_from']))->format('Y-m-d H:i') : null,
                    'show_to' => array_key_exists('show_to', $data) && $data['show_to'] ? (new \DateTime($data['show_to']))->format('Y-m-d H:i') : null,
                    'all_dates' => array_key_exists('all_dates', $data) ? (bool) $data['all_dates'] : null,
                    'tracking_status' => array_key_exists('tracking_status', $data) ? $data['tracking_status'] : null,
                    'learner_status' => array_key_exists('learner_status', $data) ? $data['learner_status'] : null
                ];
                $data['filter'] = json_encode($filterData);

                // save filter to repository
                $filter = $service->createFilter($data, $siteId, 'report-event-progress-details');

                // success
                $cookie = new SetCookie('newEPDFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/event-progress-details/filter/update', ['filter_id' => $filter['filter_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot save the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update ONE report filter
     */
    public function updateAction ()
    {
        $siteId   = $this->params('site_id');
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\EventProgressDetails\Form\FilterUpdate');

        // if a new filter has been created
        $newEPDFilter = false;
        if (isset($this->getRequest()->getCookie()->newEPDFilter)) {
            $newEPDFilter = $this->getRequest()->getCookie()->newEPDFilter;
        }
        if ($newEPDFilter) {
            $message['success'] = sprintf($this->translate('Successfully created the filter "%s"'), $newEPDFilter);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // extract data from the form values
                $data = Stdlib\ObjectUtils::extract($data);
                Stdlib\ArrayUtils::arrayPush($data, $filter);

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($filter);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update the filter learning activities
     */
    public function activitiesAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\EventProgressDetails\Form\FilterActivities');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['activity_id'] = isset($data['activity_id']) ? $data['activity_id'] : null;
                $data['filter'] = $filterData ? $filterData : null;

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $values = json_decode($filter['filter'], true);
        $values['filter_id'] = $filterId;
        $form->populateValues($values);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update the filter events
     */
    public function eventsAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form   = $this->form('Report\EventProgressDetails\Form\FilterEvents');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['event_id'] = isset($data['event_id']) ? $data['event_id'] : null;
                $data['filter'] = $filterData ? $filterData : null;

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                };
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $values = json_decode($filter['filter'], true);
        $values['filter_id'] = $filterId;
        $form->populateValues($values);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update the filter groups
     */
    public function groupsAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\EventProgressDetails\Form\FilterGroups');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                if (!isset($data['group_id'])) {
                    $data['group_id'] = null;
                }

                // create the filter
                $filterData = [];
                $filterData['group_id'] = isset($data['group_id']) ? $data['group_id'] : null;
                $data['filter'] = $filterData ? $filterData : null;

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                };
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $values = json_decode($filter['filter'], true);
        $values['filter_id'] = $filterId;
        $form->populateValues($values);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update the filter learners
     */
    public function learnersAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form  = $this->form('Report\EventProgressDetails\Form\FilterLearners');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                if (!isset($data['learner_id'])) {
                    $data['learner_id'] = null;
                }

                // create the filter
                $filterData = [];
                isset($data['learner_id']) ? $filterData['learner_id'] = $data['learner_id'] : null;
                $filterData ? $data['filter'] = $filterData : null;

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $values = json_decode($filter['filter'], true);
        $values['filter_id'] = $filterId;
        $form->populateValues($values);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Update the filter date ranges
     */
    public function rangeAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\EventProgressDetails\Form\FilterRange');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['show_from']       = isset($data['show_from']) && $data['show_from'] ? (new \DateTime($data['show_from']) )->format('Y-m-d') : null;
                $filterData['show_to']         = isset($data['show_to']) && $data['show_to']     ? (new \DateTime($data['show_to']) )->format('Y-m-d')   : null;
                $filterData['all_dates']       = isset($data['all_dates'])                       ? (bool) $data['all_dates']                             : null;
                $filterData['tracking_status'] = isset($data['tracking_status'])                 ? $data['tracking_status']                              : null;
                $filterData['learner_status']  = isset($data['learner_status'])                  ? $data['learner_status']                               : null;
                $data['filter']                = $filterData                                     ? $filterData                                           : null;

                // save filter to repository
                $filter = $service->updateFilter($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the filter "%s"'), $filter['title']);
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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $values = json_decode($filter['filter'], true);
        $values['filter_id'] = $filterId;
        $form->populateValues($values);

        return [
            'filter'  => $filter,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Display ONE filter
     */
    public function readAction ()
    {
        $filterId = $this->params('filter_id');
        $service = $this->reportFilterService();
        $filter = $service->findOneFilterById($filterId);

        return [
            'filter' => $filter
        ];
    }

    /**
     * Execute filter
     */
    public function executeAction ()
    {
        $request = $this->getRequest();
        try {
            $filterId = $this->params('filter_id');
            $service = $this->reportFilterService();
            $filter = $service->findOneFilterById($filterId);
            $values = json_decode($filter['filter'], true);

            // create a new session
            $sessionId = strtolower('r' . Stdlib\StringUtils::randomString(12));
            $session = $this->session($sessionId);
            $session->exchangeArray($values);
            $session['filter_id'] = $filterId;

            // success
            return $this->redirect()->toRoute('report/event-progress-details/report', ['session_id' => $sessionId]);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('executeEPDFilterError', 1, time() + 60 * 1);
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-details/filter/directory');
        }
    }

    /**
     * Delete filter
     */
    public function deleteAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm')) {
            try {
                // delete filter
                $service->deleteFilter($filterId);

                // success
                $cookie = new SetCookie('deleteEPDFilter', $filter['title'], time() + 60 * 1);
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/event-progress-details/filter/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteEPDFilterError', 1, time() + 60 * 1);
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/event-progress-details/filter/directory');
            }
        }

        return [
            'filter' => $filter
        ];
    }

    /**
     * Get the Report Filter service
     *
     * @return \Report\Service\FilterService
     */
    protected function reportFilterService ()
    {
        return $this->service('Report\FilterService');
    }
}