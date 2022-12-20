<?php

namespace Report\MyExperience\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ReportsController extends AbstractActionController
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
     * Display the tincan (xAPI) learning activities
     *
     * @return array
     * @throws \Exception
     */
    public function directoryAction ()
    {
        try {
            $siteId = $this->params('site_id');

            $sessionId   = $this->params('session_id');
      
            $session     = $this->session($sessionId);

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            /* @var $service \Report\MyExperience\Service\ReportService */
            $service = $this->reportService();

            // get all current tincan activities
            $activities = $service->getAllActivities($siteId);

            // get active learners
            $learners   = $this->service('Learner\Active');

            // get active groups
            $groups   = $this->service('Group\ActiveGroups');

            // get the report filters
            $filters = $this->service('Report\MyExperience\Filters');

            // get the report templates
            $templates = $this->service('Report\MyExperience\Templates');
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'activities'    => $activities,
            'options'       => $learners,
            'groups'        => $groups,
            'filters'       => $filters,
            'templates'     => $templates,
            'session'       => $session,
            'optionService' => $optionService
        ];
    }

    public function viewAction()
    {
        $results   = array();
        $siteId    = $this->params('site_id');
        $sessionId = $this->params('session_id');
        $session   = $this->session($sessionId);
        $template  = false;
        $filters   = false;

        /* @var $optionService \Tincan\Service\OptionsService */
        $optionService = $this->service('Tincan\Options');

        // process form request
        if ($post = $this->post(false)) {

            try {

                // store the selected template ID in session
                $templateId = isset($post['template_id']) ? $post['template_id'] : null;
                $session['template_id'] = is_numeric($templateId) ? $templateId : null;

                // store the selected filter ID in session
                $filterId = isset($post['filter_id']) ? $post['filter_id'] : null;
                $session['filter_id'] = is_numeric($filterId) ? $filterId : null;

                /* @var $service \Report\MyExperience\Service\ReportService */
                $service = $this->reportService();

                if (is_numeric($filterId)) {
                    // get the results using a filter
                    $filterService       = $this->reportFilterService();
                    $result              = $filterService->findOneFilterById($filterId);
                    $filter              = json_decode($result['filter'], true);
                    $filter['filter_id'] = $result['filter_id'];

                    $results = $service->generateReport($post, $optionService, $siteId, $filter);

                } else {

                    $filters = array();

                    // save the filters in session
                    // store selected activity IDs to the current session
                    unset($session['activity_iri']);
                    if (isset($post['activity_iri']) && $post['activity_iri'] != 'all') {
                        $session['activity_iri'] = $post['activity_iri'];
                        $filters['activity_iri'] = 1;
                    } else {
                        $session['activity_iri'] = isset($post['all_activities']) ? explode(",", $post['all_activities']) : false;
                        $filters['activity_iri'] = 'All (' . count($session['activity_iri']) . ')';
                    }

                    // filtering etc needs the real activity ID
                    $arr = array();
                    /* @var $learningService \Tincan\Service\TincanService */
                    $learningService = $this->learningService();
                    if (is_array($session['activity_iri'])) {
                        foreach ($session['activity_iri'] as $iri) {
                            $activity = $learningService->findOneLearningActivityByIri($iri);
                            $arr[] = $activity['activity_id'];
                        }
                        $session['activity_id'] = $arr;

                    } else {
                        // single IRI - no array available
                        $activity = $learningService->findOneLearningActivityByIri($session['activity_iri']);
                        $session['activity_id'] = array($activity['activity_id']);
                    }

                    // store selected group IDs to the current session
                    unset($session['group_id']);
                    if (isset($post['group_id']) && count($post['group_id']) >= 1) {
                        $session['group_id'] = $post['group_id'];
                        $filters['group_id'] = $session['group_id'];
                    } else {
                        $session['group_id'] = isset($post['all_groups']) ? explode(",", $post['all_groups']) : false;
                        $filters['group_id'] = 'All (' . count($session['group_id']) . ')';
                    }

                    // store selected learner IDs to the current session
                    unset($session['learner_id']);
                    if (isset($post['learner_id']) && count($post['learner_id']) >= 1) {
                        $session['learner_id'] = $post['learner_id'];
                        $filters['learner_id'] = $session['learner_id'];
                    } else {
                        $session['learner_id'] = isset($post['all_learners']) ? explode(",", $post['all_learners']) : false;
                        $filters['learner_id'] = 'All (' . count($session['learner_id']) . ')';
                    }

                    // learner status
                    unset($session['learner_status']);
                    if (isset($post['learner_status']) && $post['learner_status'] != 'all') {
                        $session['learner_status'] = $post['learner_status'];
                        $filters['learner_status'] = $session['learner_status'];
                    } else {
                        $session['learner_status'] = 'active';
                        $filters['learner_status'] = 'Active';
                    }

                    // completion status
                    unset($session['completion_status']);
                    if (isset($post['completion_status']) && $post['completion_status'] != 'all') {
                        $session['completion_status'] = $post['completion_status'];
                        $filters['completion_status'] = $session['completion_status'];
                    } else {
                        $session['completion_status'] = isset($post['all_completion']) ? explode(",", $post['all_completion']) : false;
                        $filters['completion_status'] = 'Completed, Incomplete, Passed, Failed';
                    }

                    // save the filters in case we need them ltr on
                    $session['filters'] = $filters;

                    $results = $service->generateReport($post, $optionService, $siteId);

                }

                // get a report template
                $templateService = $this->templateService();
                $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('csv', $siteId, 'tincan-report');

                // save the nicely formatted array for the report CSV generation
                $array   = $service->convertReportToArray($results);
                $session['results'] = $array;

            } catch (\Exception $e) {
                throw $e;
            }
        }

        return [
            'results'       => $results,
            'template'      => $template,
            'session'       => $session,
            'filters'       => $filters,
            'optionService' => $optionService
        ];
    }

    /**
     * Export to CSV
     */
    public function csvAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');
        try {

            $siteId = $this->params('site_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);
            $session['site_id'] = $siteId;

            // extract the values from the session as array
            $result = $session->getArrayCopy();

            $report = $result['results'];

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-myexperience');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'tincan-reports';
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
            $cookie = new SetCookie('displayILOError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/myexperience/reports/directory', ['session_id' => $sessionId]);
        }
    }

    /**
     * Export to PDF
     */
    public function pdfAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');
        try {
            $siteId = $this->params('site_id');
            $service = $this->reportService();

            $session = $this->session($sessionId);
            $session['site_id'] = $siteId;

            // extract the values from the session as array
            $result = $session->getArrayCopy();

            $report = $result['results'];

            // get the report templates
            $templates = $this->service('Report\IndividualLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-myexperience');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'tincan-reports';
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
            $cookie = new SetCookie('displayILOError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/myexperience/reports/directory', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\MyExperience\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\MyExperience\Service');
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

    /**
     * Get the Report service
     *
     * @return \Tincan\Service\TincanService
     */
    public function learningService ()
    {
        return $this->service('\Tincan\Service');
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
