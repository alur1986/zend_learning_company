<?php

namespace Report\MyLocker\Controller;

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
     * Display the categories in MyLocker
     */
    public function categoriesAction ()
    {
        $sessionId   = $this->params('session_id');
        $session     = $this->session($sessionId);
        $categoryIds = $session['category_id'] ?  : [];
        $request     = $this->getRequest();

        // form
        $form = $this->form('Report\MyLocker\Form\Categories');
        $form->get('category_id')
            ->setValue($categoryIds);

        // if an error occurred
        $message = false;
        $displayMLError = false;
        if (isset($this->getRequest()->getCookie()->displayMLError)) {
            $displayMLError = $this->getRequest()->getCookie()->displayMLError;
        }
        if ($displayMLError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected category IDs to the current session
                unset($session['category_id']);
                if (isset($data['category_id']) && !empty($data['category_id'])) {
                    $session['category_id'] = $data['category_id'];
                }

                // success
                return $this->redirect()->toRoute('report/mylocker/range', ['session_id' => $sessionId]);
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
     * Display the date range
     */
    public function rangeAction ()
    {
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\MyLocker\Form\Range');
        $filter = $session->getArrayCopy();

        // populate form with previously stored values
        foreach ($filter as $key => $value) {
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
                return $this->redirect()->toRoute('report/mylocker/report', ['session_id' => $sessionId]);
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
        $request  = $this->getRequest();
        $sessionId = $this->params('session_id');

        // set any messages passed in
        $message = false;
        $displayMLError = false;
        if (isset($this->getRequest()->getCookie()->displayMLError)) {
            $displayMLError = $this->getRequest()->getCookie()->displayMLError;
        }
        if ($displayMLError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('user_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);
            $session['site_id'] = $siteId;
            $session['learner_id'] = $learnerId;

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // process template select form submit
            if ($post = $this->post(false)) {
                try {
                    // store the selected template ID in session
                    $templateId = $post['template_id'];
                    $session['template_id'] = is_numeric($templateId) ? $templateId : null;
                }
                catch (\Exception $e) {
                    // failed
                    $message['error'] = $this->translate('Cannot change the template for the report. An internal error has occurred. Please contact your support administrator or try again later.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addErrorMessage($message['error']);
                        return $this->redirect()->refresh();
                    }
                }
            }

            // get the report templates
            $templates = $this->service('Report\MyLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-mylocker');

            return [
                'report'     => $report,
                'templates'  => $templates,
                'template'   => $template,
                'filter'     => $filter,
                'session_id' => $sessionId,
                'message'    => $message
            ];
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayMLError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/mylocker/categories', ['session_id' => $sessionId]);
        }
    }

    /**
     * Export to CSV
     */
    public function csvAction ()
    {
        $request = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('user_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the report templates
            $templates = $this->service('Report\MyLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-mylocker');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'mylocker';
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
            $cookie = new SetCookie('displayMLError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/mylocker/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Export to PDF
     */
    public function pdfAction ()
    {
        $request = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('user_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the report templates
            $templates = $this->service('Report\MyLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-mylocker');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'mylocker';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $sessionId;
            $filename = $storagePath . '.pdf';

            // create the PDF file
            $model = new \Savve\Pdf\View\Model\PdfModel();
            $model->setVariable('report', $report);
            $model->setVariable('template', $template);
            $model->setOption('outputFilename', $filename);
            $model->setOption('orientation', 'Landscape');

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayMLError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/mylocker/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Print to screen
     */
    public function printAction ()
    {
        $request = $this->getRequest();
        $sessionId = $this->params('session_id');

        try {
            $siteId = $this->params('site_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);
            $session['site_id'] = $siteId;

            // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the report templates
            $templates = $this->service('Report\MyLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-mylocker');

            // create the PDF file
            $model = new \Zend\View\Model\ViewModel([
                'report' => $report,
                'template' => $template
            ]);
            $model->setTerminal(true);

            return $model;
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayMLError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($message['error']);
            }
            return $this->redirect()->toRoute('report/mylocker/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\MyLocker\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\MyLocker\Service');
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