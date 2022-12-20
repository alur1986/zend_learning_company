<?php

namespace Report\LearningPlaylist\Controller;

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
        $filters = $service->findAllFiltersBySiteId($siteId, 'report-learning-playlist');

        $message = false;
        // if a filter has been deleted
        $deleteLPFilter = false;
        if (isset($this->getRequest()->getCookie()->deleteLPFilter)) {
            $deleteLPFilter = $this->getRequest()->getCookie()->deleteEPDFilter;
        }
        if ($deleteLPFilter) {
            $message['success'] = sprintf($this->translate('Successfully deleted the filter "%s"'), $deleteLPFilter);
        }
        // if a filter has NOT been deleted
        $deleteLPFilterError = false;
        if (isset($this->getRequest()->getCookie()->deleteLPFilterError)) {
            $deleteLPFilterError = $this->getRequest()->getCookie()->deleteLPFilterError;
        }
        if ($deleteLPFilterError == 1) {
            $message['error'] = $this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }
        // if a filter has NOT been executed
        $executeLPFilterError = false;
        if (isset($this->getRequest()->getCookie()->executeLPFilterError)) {
            $executeLPFilterError = $this->getRequest()->getCookie()->executeLPFilterError;
        }
        if ($executeLPFilterError == 1) {
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
        $form = $this->form('Report\LearningPlaylist\Form\FilterCreate');

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
                    'plan_id' => array_key_exists('plan_id', $data) ? (array) $data['plan_id'] : null,
                    'learner_id' => array_key_exists('learner_id', $data) ? (array) $data['learner_id'] : null,
                    'show_from' => array_key_exists('show_from', $data) && $data['show_from'] ? (new \DateTime($data['show_from']))->format('Y-m-d H:i') : null,
                    'show_to' => array_key_exists('show_to', $data) && $data['show_to'] ? (new \DateTime($data['show_to']))->format('Y-m-d H:i') : null,
                    'all_dates' => array_key_exists('all_dates', $data) ? (bool) $data['all_dates'] : null,
                    'tracking_status' => array_key_exists('tracking_status', $data) ? $data['tracking_status'] : null,
                    'learner_status' => array_key_exists('learner_status', $data) ? $data['learner_status'] : null,
                    'aggregated_output' => array_key_exists('aggregated_output', $data) ? $data['aggregated_output'] : null
                ];
                $data['filter'] = json_encode($filterData);

                // save filter to repository
                $filter = $service->createFilter($data, $siteId, 'report-learning-playlist');

                // success
                $cookie = new SetCookie('newLPFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/learning-playlist/filter/update', ['filter_id' => $filter['filter_id']]);
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
        $form = $this->form('Report\LearningPlaylist\Form\FilterUpdate');

        // if a new filter has been created
        $newLPFilter = false;
        if (isset($this->getRequest()->getCookie()->newLPFilter)) {
            $newLPFilter = $this->getRequest()->getCookie()->newEPDFilter;
        }
        if ($newLPFilter) {
            $message['success'] = sprintf($this->translate('Successfully created the filter "%s"'), $newLPFilter);
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
        $form = $this->form('Report\LearningPlaylist\Form\FilterActivities');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['plan_id'] = isset($data['plan_id']) ? $data['plan_id'] : null;
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

        /** @var $playlistService \LearningPlan\Service\LearningPlan */
        $playlistService = $this->playlistService();
        $plans = $values['plan_id'];
        $valueOptions = [];
        foreach ($plans as $plan) {
            $playlist = $playlistService->findOneLearningPlanById( $plan );
            $valueOptions[] = [
                'label' => $playlist['title'],
                'value' => $plan
            ];
        }

        $form->get('plan_id')->setValueOptions( $valueOptions );

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
        $form  = $this->form('Report\LearningPlaylist\Form\FilterLearners');

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
        $form = $this->form('Report\LearningPlaylist\Form\FilterRange');

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
                $filterData['aggregated_output']  = isset($data['aggregated_output'])            ? $data['aggregated_output']                            : null;
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
            return $this->redirect()->toRoute('report/learning-playlist/report', ['session_id' => $sessionId]);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('executeLPFilterError', 1, time() + 60 * 1);
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/learning-playlist/filter/directory');
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
                $cookie = new SetCookie('deleteLPFilter', $filter['title'], time() + 60 * 1);
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/learning-playlist/filter/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteLPFilterError', 1, time() + 60 * 1);
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/learning-playlist/filter/directory');
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

    /**
     * Get the LearningPlan service
     *
     * @return \LearningPlan\Service\LearningPlan
     */
    protected function playlistService ()
    {
        return $this->service('LearningPlan\Service');
    }
}