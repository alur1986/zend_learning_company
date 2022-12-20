<?php

namespace Report\MyLocker\Controller;

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
        $filters = $service->findAllFiltersBySiteId($siteId, 'report-mylocker');

        $message = false;
        // if a filter has been deleted
        $deleteMLFilter = false;
        if (isset($this->getRequest()->getCookie()->deleteMLFilter)) {
            $deleteMLFilter = $this->getRequest()->getCookie()->deleteMLFilter;
        }
        if ($deleteMLFilter) {
            $message['success'] = sprintf($this->translate('Successfully deleted the filter "%s"'), $deleteMLFilter);
        }
        // if a filter has NOT been deleted
        $deleteMLFilterError = false;
        if (isset($this->getRequest()->getCookie()->deleteMLFilterError)) {
            $deleteMLFilterError = $this->getRequest()->getCookie()->deleteMLFilterError;
        }
        if ($deleteMLFilterError == 1) {
            $message['error'] = $this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.');
        }
        // if a filter has NOT been executed
        $executeMLFilterError = false;
        if (isset($this->getRequest()->getCookie()->executeMLFilterError)) {
            $executeMLFilterError = $this->getRequest()->getCookie()->executeMLFilterError;
        }
        if ($executeMLFilterError == 1) {
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

        $session = $this->session($sessionId);
        $filter  = $session->getArrayCopy();

        // form
        $form = $this->form('Report\MyLocker\Form\FilterCreate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                $data = Stdlib\ObjectUtils::extract($data);
                Stdlib\ArrayUtils::arrayPush($data, $filter);
                $data['filter'] = json_encode([
                    'site_id' => $siteId,
                    'category_id' => array_key_exists('category_id', $data) ? (array) $data['category_id'] : null,
                    'learner_id' => array_key_exists('learner_id', $data) ? (array) $data['learner_id'] : null,
                    'verification_status' => array_key_exists('verification_status', $data) ? $data['verification_status'] : null
                ]);

                // save filter to repository
                $filter = $service->createFilter($data, $siteId, 'report-mylocker');

                // success
                $cookie = new SetCookie('newMLFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/mylocker/filter/update', ['filter_id' => $filter['filter_id']]);
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
        $form = $this->form('Report\MyLocker\Form\FilterUpdate');

        // if a new filter has been created
        $newMLFilter = false;
        if (isset($this->getRequest()->getCookie()->newMLFilter)) {
            $newMLFilter = $this->getRequest()->getCookie()->newMLFilter;
        }
        if ($newMLFilter) {
            $message['success'] = sprintf($this->translate('Successfully created the filter "%s"'), $newMLFilter);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['site_id'] = $siteId;
                isset($data['category_id']) ? $filterData['category_id'] = $data['category_id'] : null;
                isset($data['learner_id']) ? $filterData['learner_id'] = $data['learner_id'] : nulll;
                isset($data['verification_status']) ? $filterData['verification_status'] = $data['verification_status'] : null;
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
     * Update the filter categories
     */
    public function categoriesAction ()
    {
        $filterId = $this->params('filter_id');
        $service  = $this->reportFilterService();
        $filter   = $service->findOneFilterById($filterId);
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\MyLocker\Form\FilterCategories');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create the filter
                $filterData = [];
                $filterData['category_id'] = isset($data['category_id']) ? $data['category_id'] : null;
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
        $form = $this->form('Report\MyLocker\Form\FilterRange');

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
            return $this->redirect()->toRoute('report/mylocker/report', ['session_id' => $sessionId]);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('executeMLFilterError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot load the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/mylocker/filter/directory');
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
                $cookie = new SetCookie('deleteMLFilter', $filter['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the filter "%s"'), $filter['title']));
                }
                return $this->redirect()->toRoute('report/mylocker/filter/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteMLFilterError', 1, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the filter. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/mylocker/filter/directory');
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