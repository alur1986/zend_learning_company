<?php

namespace Report\LearningPlaylist\Controller;

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
        $planIds     = $session['plan_id'] ?  : [];
        $request     = $this->getRequest();
        $siteId      = $this->params('site_id');

        // form
        $form = $this->form('Report\LearningPlaylist\Form\Activities');

        /* @var $options \Report\Service\Options */
        $plansService = $this->service('LearningPlan\Service');
        $options = $plansService->findAllBySiteId($siteId);
    //    $columns = $options['learning_progress_details']['available_template_columns'];
        $valueOptions = [];
        foreach ($options as $column) {
            $valueOptions[] = [
                'label' => $column['title'],
                'value' => $column['plan_id'],
                'status' => $column['status']
            ];
        }
        $form->get('plan_id')
            ->setValueOptions($valueOptions);

    //    $form->get('plan_id')
    //        ->setValue($planIds);

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
                unset($session['plan_id']);
                if (isset($data['plan_id']) && !empty($data['plan_id'])) {
                    $session['plan_id'] = $data['plan_id'];
                }

                // success
                return $this->redirect()->toRoute('report/learning-playlist/learners', ['session_id' => $sessionId]);
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
     * Display the learners
     */
    public function learnersAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\LearningPlaylist\Form\Learners');

        //get the user's current role
        $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
        //Set to guest access if no user level is found
        $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;

        // get learners
     //   $getLearner = $this->getViewHelper( 'Learner' );
     //   $learner = $getLearner();

        $planId = $session['plan_id'];
        /* @var $learnerService \Report\LearningPlaylist\Service\ReportService */
        $learnerService = $this->reportService();
        $distributions = $learnerService->getDistributedLearners($planId);
        $learners = array();

        if (is_array(\Savve\Stdlib\ArrayUtils::toArray($distributions))) {
            foreach($distributions as $distro) {
                $learners[] = $distro['learner'];
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
                return $this->redirect()->toRoute('report/learning-playlist/range', ['session_id' => $sessionId]);
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
        $form = $this->form('Report\LearningPlaylist\Form\Range');

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
                return $this->redirect()->toRoute('report/learning-playlist/report', ['session_id' => $sessionId]);
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

        foreach ($values as $key => $value) {
            if ($form->has($key)) {
                $form->get($key)->setValue($value);
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
            $templates = $this->service('Report\LearningPlaylist\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;

            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-playlist');

            // execute the report
            $service = $this->reportService();
            $report = $service->report($filter);

            return [
                'report'     => $report,
                'templates'  => $templates,
                'template'   => $template,
                'filter'     => $filter,
                'session_id' => $sessionId,
                'siteId'     => $siteId,
                'message'     => $message
            ];
        }
        catch (\Exception $e) {
            // failed
            var_dump($e); die;
            $cookie = new SetCookie('displayLPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot process the report. An internal error has occurred. %s Please contact your support administrator or try again later.'), $e->getMessage()));
            }
            return $this->redirect()->toRoute('report/learning-playlist/activities', ['session_id' => $sessionId]);
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
	        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-playlist');

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'learning-playlist';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $sessionId;
            $filename = $storagePath . '.csv';

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('report', $report);
            $model->setVariable('siteId', $siteId);
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
            return $this->redirect()->toRoute('report/learning-playlist/report', ['session_id' => $sessionId]);
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
	        $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-playlist');

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'learning-playlist';
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
            return $this->redirect()->toRoute('report/learning-playlist/report', ['session_id' => $sessionId]);
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
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-learning-playlist');

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
            return $this->redirect()->toRoute('report/learning-playlist/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\LearningPlaylist\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\LearningPlaylist\Service');
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