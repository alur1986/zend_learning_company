<?php

namespace Report\XAPIInteractions\Controller;

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
        $filters = $service->findAllFiltersBySiteId($siteId, 'report-xapi-interactions');

        $message = false;
        // if a filter has been deleted
        $deleteLPDFilter = false;
        if (isset($this->getRequest()->getCookie()->deleteLPDFilter)) {
            $deleteLPDFilter = $this->getRequest()->getCookie()->deleteLPDFilter;
        }
        if ($deleteLPDFilter) {
            $message['success'] = sprintf($this->translate('Successfully deleted the filter "%s"'), $deleteLPDFilter);
        }
        // if a filter has NOT been deleted
        $deleteLPDFilterError = false;
        if (isset($this->getRequest()->getCookie()->deleteLPDFilterError)) {
            $deleteLPDFilterError = $this->getRequest()->getCookie()->deleteLPDFilterError;
        }
        if ($deleteLPDFilterError == 1) {
            $message['error'] = $this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }
        // if a filter has NOT been executed
        $executeLPDFilterError = false;
        if (isset($this->getRequest()->getCookie()->executeLPDFilterError)) {
            $executeLPDFilterError = $this->getRequest()->getCookie()->executeLPDFilterError;
        }
        if ($executeLPDFilterError == 1) {
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
        $form = $this->form('Report\XAPIInteractions\Form\FilterCreate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                $data = Stdlib\ObjectUtils::extract($data);
                Stdlib\ArrayUtils::arrayPush($data, $filter);
                $data['filter'] = json_encode([
                    'site_id' => $siteId,
                    'activityId' => array_key_exists('activityId', $data) ? (array) $data['activityId'] : null,
                    'group_id' => array_key_exists('group_id', $data) ? (array) $data['group_id'] : null,
                    'learner_id' => array_key_exists('learner_id', $data) ? (array) $data['learner_id'] : null,
                    'show_from' => array_key_exists('show_from', $data) && $data['show_from'] ? (new \DateTime($data['show_from']))->format('Y-m-d H:i:s') : null,
                    'show_to' => array_key_exists('show_to', $data) && $data['show_to'] ? (new \DateTime($data['show_to']))->format('Y-m-d H:i:s') : null,
                    'all_dates' => array_key_exists('all_dates', $data) ? (bool) $data['all_dates'] : null,
                    'learner_status' => array_key_exists('learner_status', $data) ? $data['learner_status'] : null,
                    'action_verb' => array_key_exists('action_verb', $data) ? $data['action_verb'] : null,
                    'show_assessment_only' => array_key_exists('show_assessment_only', $data) ? (bool) $data['show_assessment_only'] : null,
                ]);

                // save filter to repository
                $filter = $service->createFilter($data, $siteId, 'report-xapi-interactions');

                // success
                $cookie = new SetCookie('newLPDFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/xapi-interactions/filter/update', ['filter_id' => $filter['filter_id']]);
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
        $form = $this->form('Report\XAPIInteractions\Form\FilterUpdate');

        // if a new filter has been created
        $newLPDFilter = false;
        if (isset($this->getRequest()->getCookie()->newLPDFilter)) {
            $newLPDFilter = $this->getRequest()->getCookie()->newLPDFilter;
        }
        if ($newLPDFilter) {
            $message['success'] = sprintf($this->translate('Successfully created the filter "%s"'), $newLPDFilter);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['site_id'] = $siteId;
                isset($data['activityId']) ? $filterData['activityId'] = $data['activityId'] : null;
                isset($data['group_id']) ? $filterData['group_id'] = $data['group_id'] : null;
                isset($data['learner_id']) ? $filterData['learner_id'] = $data['learner_id'] : null;
                isset($data['show_from']) ? $filterData['show_from'] = (new \DateTime($data['show_from']) )->format('Y-m-d H:i:s') : null;
                isset($data['show_to']) ? $filterData['show_to'] = (new \DateTime($data['show_to']) )->format('Y-m-d H:i:s') : null;
                isset($data['all_dates']) ? $filterData['all_dates'] = (bool) $data['all_dates'] : null;
                isset($data['learner_status']) ? $filterData['learner_status'] = $data['learner_status'] : null;
                isset($data['show_assessment_only']) ? $filterData['show_assessment_only'] = (bool) $data['show_assessment_only'] : null;
                isset($data['action_verb']) ? $filterData['action_verb'] = $data['action_verb'] : null;
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
        $siteId   = $this->params('site_id');
        $message  = false;

        // form
        $form   = $this->form('Report\XAPIInteractions\Form\FilterActivities');
        $form->get('activityId')
            ->setValueOptions($this->reportService()->findAllTincanActivities($siteId));

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['activityId'] = isset($data['activityId']) ? $data['activityId'] : null;
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
        $form = $this->form('Report\XAPIInteractions\Form\FilterGroups');

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
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $$message['error'] = $this->translate('Cannot update the filter. An internal error has occurred. Please contact your support administrator or try again later.');
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
        $form = $this->form('Report\XAPIInteractions\Form\FilterLearners');

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
                $filterData['learner_id'] = isset($data['learner_id']) ? $data['learner_id'] : null;
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
        $form = $this->form('Report\XAPIInteractions\Form\FilterRange');

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
                $filterData['show_from']       = isset($data['show_from']) && $data['show_from'] ? (new \DateTime($data['show_from']) )->format('Y-m-d H:i:s') : null;
                $filterData['show_to']         = isset($data['show_to']) && $data['show_to']     ? (new \DateTime($data['show_to']) )->format('Y-m-d H:i:s')   : null;
                $filterData['all_dates']       = isset($data['all_dates'])                       ? (bool) $data['all_dates']                             : null;
                $filterData['learner_status']  = isset($data['learner_status'])                  ? $data['learner_status']                               : null;
                $filterData['show_assessment_only']       = isset($data['show_assessment_only'])                       ? (bool) $data['show_assessment_only']                             : null;
                $filterData['action_verb'] = isset($data['action_verb'])                 ? $data['action_verb']                              : null;
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
            return $this->redirect()->toRoute('report/xapi-interactions/report', ['session_id' => $sessionId]);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('executeLPDFilterError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/xapi-interactions/filter/directory');
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
                $cookie = new SetCookie('deleteLPDFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/xapi-interactions/filter/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteLPDFilterError', 1, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/xapi-interactions/filter/directory');
            }
        }

        return [
            'filter' => $filter
        ];
    }

    /**
     * Get the Report service
     *
     * @return \Report\XAPIInteractions\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\XAPIInteractions\Service');
    }

    /**
     * Get the Report Filter service
     *
     * @return \Report\Service\FilterService
     */
    public function reportFilterService ()
    {
        return $this->service('Report\FilterService');
    }
}
