<?php

namespace Report\MyExperience\Controller;

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
        $filters = $service->findAllFiltersBySiteId($siteId, 'report-myexperience');

        $message = false;
        // if a filter has been deleted
        $deleteMyExFilter = false;
        if (isset($this->getRequest()->getCookie()->deleteMyExFilter)) {
            $deleteMyExFilter = $this->getRequest()->getCookie()->deleteMyExFilter;
        }
        if ($deleteMyExFilter) {
            $message['success'] = sprintf($this->translate('Successfully deleted the filter "%s"'), $deleteMyExFilter);
        }
        // if a filter has NOT been deleted
        $deleteMyExFilterError = false;
        if (isset($this->getRequest()->getCookie()->deleteMyExFilterError)) {
            $deleteMyExFilterError = $this->getRequest()->getCookie()->deleteMyExFilterError;
        }
        if ($deleteMyExFilterError == 1) {
            $message['error'] = $this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }
        // if a filter has NOT been executed
        $executeMyExFilterError = false;
        if (isset($this->getRequest()->getCookie()->executeMyExFilterError)) {
            $executeMyExFilterError = $this->getRequest()->getCookie()->executeMyExFilterError;
        }
        if ($executeMyExFilterError == 1) {
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

  //      $return    = $this->getRequest()->getParam(); // Request('return', '0');//$request->getParams('return');

        // form
        $form = $this->form('Report\MyExperience\Form\FilterCreate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                $return = isset($data['return_data']) ? true : false;

                $data = Stdlib\ObjectUtils::extract($data);
                Stdlib\ArrayUtils::arrayPush($data, $filter);
                $data['filter'] = json_encode([
                    'site_id' => $siteId,
                    'activity_id' => array_key_exists('activity_id', $data) ? (array) $data['activity_id'] : null,
                    'activity_iri' => array_key_exists('activity_iri', $data) ? (array) $data['activity_iri'] : null,
                    'group_id' => array_key_exists('group_id', $data) ? (array) $data['group_id'] : null,
                    'learner_id' => array_key_exists('learner_id', $data) ? (array) $data['learner_id'] : null,
                    'completion_status' => array_key_exists('completion_status', $data) ? $data['completion_status'] : null,
                    'learner_status' => array_key_exists('learner_status', $data) ? $data['learner_status'] : null
                ]);

                // save filter to repository
                $filter = $service->createFilter($data, $siteId, 'report-myexperience');

                // success
                // return data for the JSON response only
                if (isset($post['return_data']) && $post['return_data'] == 'filter_id') {

                    // send an array to the view response
                    $result = array(
                        'item' => 'Filter',
                        'id'   => $filter['filter_id'],
                        'name' => $filter['title'],
                        'success' => true
                    );

                    // the view will use this Var to create a JSON response
                    return [
                        'result' => $result
                    ];


                } else {

                    if ($request->isXmlHttpRequest()) {
                        $cookie = new SetCookie('newMyExFilter', $filter['title'], time() + 60 * 1, '/');
                        $this->getResponse()->getHeaders()->addHeader($cookie);
                    }
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the filter "%s"'), $filter['title']));
                    }
                //    if ($return == true) {
                //        echo 'Created new Filter: ' . $filter['filter_id'];
                //    } else {
                        return $this->redirect()->toRoute('report/myexperience/filter/update', ['filter_id' => $filter['filter_id']]);
                //    }
                }
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
            'session' => $session,
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
        $form = $this->form('Report\MyExperience\Form\FilterUpdate');

        // if a new filter has been created
        $newMyExFilter = false;
        if (isset($this->getRequest()->getCookie()->newMyExFilter)) {
            $newMyExFilter = $this->getRequest()->getCookie()->newMyExFilter;
        }
        if ($newMyExFilter) {
            $message['success'] = sprintf($this->translate('Successfully created the filter "%s"'), $newMyExFilter);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                isset($data['activity_id']) ? $filterData['activity_id'] = $data['activity_id'] : null;
                isset($data['activity_iri']) ? $filterData['activity_iri'] = $data['activity_iri'] : null;
                isset($data['group_id']) ? $filterData['group_id'] = $data['group_id'] : null;
                isset($data['learner_id']) ? $filterData['learner_id'] = $data['learner_id'] : null;
                isset($data['completion_status']) ? $filterData['completion_status'] = $data['completion_status'] : null;
                isset($data['learner_status']) ? $filterData['learner_status'] = $data['learner_status'] : null;
                $data['filter'] = isset($filterData) ? $filterData : null;

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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred: ' . $e->getMessage() . ' Please contact your support administrator or try again later.');
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
        $form   = $this->form('Report\MyExperience\Form\FilterActivities');

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
        $form = $this->form('Report\MyExperience\Form\FilterGroups');

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
        $form = $this->form('Report\MyExperience\Form\FilterLearners');

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
        $form = $this->form('Report\MyExperience\Form\FilterRange');

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
            //    $filterData['show_from']       = isset($data['show_from']) && $data['show_from'] ? (new \DateTime($data['show_from']) )->format('Y-m-d') : null;
            //    $filterData['show_to']         = isset($data['show_to']) && $data['show_to']     ? (new \DateTime($data['show_to']) )->format('Y-m-d')   : null;
            //    $filterData['all_dates']       = isset($data['all_dates'])                       ? (bool) $data['all_dates']                             : null;
                $filterData['completion_status'] = isset($data['completion_status'])             ? $data['completion_status']                            : null;
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
                $message['error'] = $this->translate('Cannot update the filter. An internal error has occurred: ' . $e->getMessage() . ' Please contact your support administrator or try again later.');
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
            return $this->redirect()->toRoute('report/myexperience/report', ['session_id' => $sessionId]);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('executeMyExFilterError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/myexperience/filter/directory');
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
                if ($request->isXmlHttpRequest()) {
                    $cookie = new SetCookie('deleteMyExFilter', $filter['title'], time() + 60 * 1, '/');
                    $this->getResponse()->getHeaders()->addHeader($cookie);
                }
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/myexperience/filter/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteMyExFilterError', 1, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/myexperience/filter/directory');
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
    public function reportFilterService ()
    {
        return $this->service('Report\FilterService');
    }
}