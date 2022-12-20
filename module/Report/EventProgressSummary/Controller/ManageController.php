<?php

namespace Report\EventProgressSummary\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ManageController extends AbstractActionController
{

    /**
     * Display the tools for this report
     */
    public function toolsAction ()
    {
        // placeholder, do not remove
    }

    /**
     * Start the report
     */
    public function startAction ()
    {
        // placeholder, do not remove
    }

    /**
     * Display the learning activities
     */
    public function activitiesAction ()
    {
        $sessionId   = $this->params('session_id');
        $session     = $this->session($sessionId);
        $activityIds = $session['activity_id'] ?  : [];
        $request     = $this->getRequest();

        // form
        $form = $this->form('Report\EventProgressSummary\Form\Activities');
        $form->get('activity_id')
            ->setValue($activityIds);

        // if an error occurred
        $message    = false;
        $displayEPSError = false;
        if (isset($this->getRequest()->getCookie()->displayEPSError)) {
            $displayEPSError = $this->getRequest()->getCookie()->displayEPSError;
        }
        if ($displayEPSError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected activity IDs to the current session
                unset($session['activity_id']);
                if (isset($data['activity_id']) && !empty($data['activity_id'])) {
                    $session['activity_id'] = $data['activity_id'];
                }

                // success
                return $this->redirect()->toRoute('report/event-progress-summary/events', ['session_id' => $sessionId]);
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Display the learning activities events
     */
    public function eventsAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $eventIds  = $session['event_id'] ?  : [];
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\EventProgressSummary\Form\Events');
        $form->get('event_id')
            ->setValue($eventIds);

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected activity IDs to the current session
                unset($session['event_id']);
                if (isset($data['event_id']) && !empty($data['event_id'])) {
                    $session['event_id'] = $data['event_id'];
                }

                // success
                return $this->redirect()->toRoute('report/event-progress-summary/groups', ['session_id' => $sessionId]);
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Display the groups
     */
    public function groupsAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $groupIds  = $session['group_id'] ?  : [];
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\EventProgressSummary\Form\Groups');
        $form->get('group_id')
            ->setValue($groupIds);

        $siteId   = $this->getParam('site_id');
        $getLearner = $this->getViewHelper( 'Learner' );
        $learner = $getLearner();
        $userId  = $learner['userId']; // send to Service for admin priviledge check
        $service = $this->service('Group\Service');
        $groups  = $service->findAllGroupDetailsBySiteId($siteId, $userId);
        $index   = 0;

        //get the user's current role
        $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
        //Set to guest access if no user level is found
        $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;
        foreach ($groups as $group) {
            $groupAdmins = array();
            $groupAdminString = '';
            if (count($group['admins'])) {
                foreach ($group['admins'] as $admin){
                    $learner = $admin['learner'];
                    $groupAdmins[] = array("name" => $learner['first_name'] . ' ' . $learner['last_name']);
                }
            }
            $numLearners = false;
            if ($group['number_of_learners'] > 0) {
                $numLearners = $group['number_of_learners'];
            } elseif ($group['number_learners'] > 0) {
                $numLearners = $group['number_learners'];
            }
            if ($currentUserLevel == 500) {
                // only load up groups administered by this learner
                if ($group['isAdmin'] == true) {
                    $output[] = array("group_id" => $group['group_id'], "name" => $group['name'], "admins" => $groupAdmins, "num_learners" => $numLearners);
                }
            } else {
                $output[] = array("group_id" => $group['group_id'], "name" => $group['name'], "admins" => $groupAdmins, "num_learners" => $numLearners);
            }
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected group IDs to the current session
                unset($session['group_id']);
                if (isset($data['group_id']) && !empty($data['group_id'])) {
                    $session['group_id'] = $data['group_id'];
                }

                // success
                return $this->redirect()->toRoute('report/event-progress-summary/range', ['session_id' => $sessionId]);
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $session = ((isset($session) && is_array($session)) ? $session : array());
        return [
            'form'    => $form,
            'message' => $message,
            'options' => $output,
            'session' => $session
        ];
    }

    /**
     * Display the date range
     */
    public function rangeAction ()
    {
        $sessionId = $this->params('session_id');
        $session = $this->session($sessionId);
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\EventProgressSummary\Form\Range');

        // populate form with previously stored values
        foreach ($session->getArrayCopy() as $key => $value) {
            if ($form->has($key)) {
                $form->get($key)
                    ->setValue($value);
            }
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected parameters to the session
                foreach ($data as $key => $value) {
                    unset($session[$key]);
                    if (isset($data[$key]) && !is_null($data[$key])) {
                        $session[$key] = $data[$key];
                    }
                }

                // success
                return $this->redirect()->toRoute('report/event-progress-summary/report', ['session_id' => $sessionId]);
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Execute the report
     */
    public function reportAction ()
    {
        $sessionId = $this->params('session_id');
        $siteId    = $this->params('site_id');
        $service   = $this->reportService();
        $request   = $this->getRequest();
        $message   = false;

        $session   = $this->session($sessionId);
        $session['site_id'] = $siteId;

        // get the report templates
        $templates = $this->service('Report\EventProgressSummary\Templates');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // store the selected template ID in session
                $templateId = $post['template_id'];
                $session['template_id'] = $templateId;
            }
            catch (\Exception $e) {
                // failed
            }
        }

        // get the current selected template
        $templateService = $this->templateService();
        $templateId = isset($session['template_id']) ? $session['template_id'] : null;
        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-event-progress-summary');

        // if an error occurred
        $displayEPSError = false;
        if (isset($this->getRequest()->getCookie()->displayEPSError)) {
            $displayEPSError = $this->getRequest()->getCookie()->displayEPSError;
        }
        if ($displayEPSError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        try {
            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayEPSError', 1, time() + 60 * 1);
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.')));
            }
            return $this->redirect()->toRoute('report/event-progress-summary/activities', ['session_id' =>  $sessionId]);
        }

        return [
            'report'     => $report,
            'template'   => $template,
            'filter'     => $filter,
            'templates'  => $templates,
            'session_id' => $sessionId,
            'message'    => $message
        ];
    }

    /**
     * Download the report as CSV
     */
    public function csvAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $siteId    = $this->params('site_id');
        $service   = $this->reportService();
        $request   = $this->getRequest();

        // get the current selected template
        $templateService = $this->templateService();
        $templateId = $session['template_id'] ?  : null;
        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-event-progress-summary');

        try {
            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'event-progress-summary';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $sessionId;

            $filename = $storagePath . '.csv';

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('template', $template);
            $model->setVariable('report', $report);
            $model->setOption('outputFilename', $filename);

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayEPSError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-summary/report', ['session_id' => $sessionId]);
        }

        return [
            'report' => $report
        ];
    }

    /**
     * Download the report as PDF
     */
    public function pdfAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $siteId    = $this->params('site_id');
        $service   = $this->reportService();
        $request   = $this->getRequest();

        // get the current selected template
        $templateService = $this->templateService();
        $templateId = $session['template_id'] ?  : null;
        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-event-progress-summary');

        try {
            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'event-progress-summary';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $sessionId;

            $filename = $storagePath . '.pdf';

            // create the PDF file
            $model = new \Savve\Pdf\View\Model\PdfModel();
            $model->setVariable('template', $template);
            $model->setVariable('report', $report);
            $model->setOption('outputFilename', $filename);
            $model->setOption('orientation', 'Landscape');

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayEPSError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-summary/report', ['session_id' => $sessionId]);
        }

        return [
            'report' => $report
        ];
    }

    /**
     * Print the report
     */
    public function printAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $siteId    = $this->params('site_id');
        $service   = $this->reportService();
        $request   = $this->getRequest();

        // get the current selected template
        $templateService = $this->templateService();
        $templateId = $session['template_id'] ?  : null;
        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-event-progress-summary');

        try {
            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // create a print view
            $view = new \Zend\View\Model\ViewModel([
                'report' => $report,
                'template' => $template
            ]);
            $view->setTerminal(true);
            return $view;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayEPSError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-summary/report', ['session_id' =>  $sessionId]);
        }

        return [
            'report' => $report,
            'template' => $template
        ];
    }

    /**
     * Get the Report service
     *
     * @return \Report\EventProgressSummary\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\EventProgressSummary\Service');
    }

    /**
     * Get the report template service
     *
     * @return \Report\Service\TemplateService
     */
    public function templateService ()
    {
        return $this->service('Report\TemplateService');
    }
}