<?php

namespace Report\LearningProgressDetails\Controller;

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
        $form = $this->form('Report\LearningProgressDetails\Form\Activities');
        $form->get('activity_id')
            ->setValue($activityIds);

        // if an error occurred
        $message = false;
        $displayLPDError = false;
        if (isset($this->getRequest()->getCookie()->displayLPDError)) {
            $displayLPDError = $this->getRequest()->getCookie()->displayLPDError;
        }
        if ($displayLPDError == 1) {
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
                return $this->redirect()->toRoute('report/learning-progress-details/groups', ['session_id' => $sessionId]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
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
        $request   = $this->getRequest();
        $message   = false;
        $output    = array();

        // form
        $form = $this->form('Report\LearningProgressDetails\Form\Groups');

        $siteId   = $this->getParam('site_id');
        $getLearner = $this->getViewHelper( 'Learner' );
        $learner = $getLearner();
        $service = $this->service('Group\Service');
        $groups  = $service->findAllGroupDetailsBySiteId($siteId, $learner['userId']);

        //get the user's current role
        $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
        //Set to guest access if no user level is found
        $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;
        foreach ($groups as $group) {
            $groupAdmins = array();
            if (count($group['admins'])) {
                foreach ($group['admins'] as $admin){
                    $learner = $admin['learner'];
                    $groupAdmins[] = array("name" => $learner['first_name'] . ' ' . $learner['last_name']);
                }
            }
            $numLearners = false;
            if (isset($group['number_of_learners']) && $group['number_of_learners'] > 0) {
                $numLearners = $group['number_of_learners'];
            } elseif (isset($group['number_learners']) && $group['number_learners'] > 0) {
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
                return $this->redirect()->toRoute('report/learning-progress-details/learners', ['session_id' => $sessionId]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
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
            'options' => $output,
            'session' => $session,
            'message' => $message
        ];
    }

    /**
     * Display the learners
     */
    public function learnersAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\LearningProgressDetails\Form\Learners');

        $groupIds = $session['group_id'];
        /* @var $groupLearnerService \Group\Learner\Service\GroupLearnerService */
        $groupLearnerService = $this->service('Group\Learner\Service');
        if (count($groupIds)) {
            $learners = $groupLearnerService->fetchAllTheLearnersInGroupId($groupIds,['new','active','inactive']);
        } else {
            //get the user's current role
            $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
            //Set to guest access if no user level is found
            $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;

            // get learner
            $getLearner = $this->getViewHelper( 'Learner' );
            $learner = $getLearner();

            // get learners
            if ($currentUserLevel == 500) {
                // is group admin
                $learners = $groupLearnerService->findAllLearnersByGroupAdmin($learner['userId']);

            } else {
                $siteId = $this->params('site_id');
                /* @var $learnerService \Learner\Service\LearnerService */
                $learnerService = $this->service('Learner\Service');
                $learners       = $learnerService->findAllBySiteIdWithLimit ($siteId, 0);
            }
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected learner IDs to the current session
                unset($session['learner_id']);
                if (isset($data['learner_id']) && !empty($data['learner_id'])) {
                    $session['learner_id'] = $data['learner_id'];
                }

                // success
                return $this->redirect()->toRoute('report/learning-progress-details/range', ['session_id' => $sessionId]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
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
            'options' => $learners,
            'session' => $session
        ];
    }

    /**
     * Display the date range
     */
    public function rangeAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $values    = $session->getArrayCopy();
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\LearningProgressDetails\Form\Range');
        foreach ($values as $key => $value) {
            if ($form->has($key)) {
                $form->get($key)->setValue($value);
            }
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected parameters to the session if
                foreach ($data as $key => $value) {
                    unset($session[$key]);
                    if (isset($data[$key]) && !is_null($data[$key])) {
                        $session[$key] = $data[$key];
                    }
                }

                // success
                return $this->redirect()->toRoute('report/learning-progress-details/report', ['session_id' => $sessionId]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
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
        $session   = $this->session($sessionId);
        $request   = $this->getRequest();
        $session['site_id'] = $siteId;

        // if an error occurred
        $message = false;
        $displayLPDError = false;
        if (isset($this->getRequest()->getCookie()->displayLPDError)) {
            $displayLPDError = $this->getRequest()->getCookie()->displayLPDError;
        }
        if ($displayLPDError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        // process form submit
        if ($post = $this->post(false)) {

            try {
                // store the selected template ID in session
                $templateId = $post['template_id'];
                $session['template_id'] = $templateId;

                // return $this->redirect()->refresh();
            }
            catch (\Exception $e) {
                // failed
            }
        }

        try {
            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // get the report templates
            $templates = $this->service('Report\LearningProgressDetails\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;

            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-progress-details');

            // execute the report
            $service = $this->reportService();
            $report = $service->report($filter, $siteId);

            return [
                'report'     => $report,
                'templates'  => $templates,
                'template'   => $template,
                'filter'     => $filter,
                'session_id' => $sessionId,
                'message     => $message'
            ];
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayLPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/learning-progress-details/activities', ['session_id' => $sessionId]);
        }
    }

    /**
     * Download the report as CSV
     */
    public function csvAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
	        $session = $this->session($sessionId);
	        $siteId = $this->params('site_id');
	        $service = $this->reportService();

	        // get the current selected template
	        $templateService = $this->templateService();
	        $templateId = $session['template_id'] ?  : null;
	        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-progress-details');

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'learning-progress-details';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $sessionId;
            $filename = $storagePath . '.csv';

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('report', $report);
            $model->setVariable('template', $template);
            $model->setOption('outputFilename', $filename);

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayLPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/learning-progress-details/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Download the report as PDF
     */
    public function pdfAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
	        $session = $this->session($sessionId);
	        $siteId = $this->params('site_id');
	        $service = $this->reportService();

	        // get the current selected template
	        $templateService = $this->templateService();
	        $templateId = $session['template_id'] ?  : null;
	        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-progress-details');

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'learning-progress-details';
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
            $cookie = new SetCookie('displayLPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/learning-progress-details/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Print the report
     */
    public function printAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
            $session = $this->session($sessionId);
            $siteId = $this->params('site_id');
            $service = $this->reportService();

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-progress-details');

            // create a print view
            $model = new \Zend\View\Model\ViewModel([
                'report' => $report,
                'template' => $template
            ]);
            $model->setTerminal(true);

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayLPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/learning-progress-details/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\LearningProgressDetails\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\LearningProgressDetails\Service');
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