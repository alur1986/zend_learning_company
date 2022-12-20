<?php

namespace Report\MyLearning\Controller;

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
        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('user_id');
            $trackingStatus = null;

            $service = $this->reportService();

            // create the filter manually
            $filter['site_id']    = $siteId;
            $filter['learner_id'] = $learnerId;
            $filter['order_by']   = ['tracking_status_level DESC', 'plans.title ASC'];

            // execute the report
            $report = $service->report($filter);

        }
        catch (\Exception $e) {
            throw $e;
        }

        return ['report' => $report];
    }

    /**
     * Export to PDF
     */
    public function pdfAction ()
    {
        try {
            $siteId     = $this->params('site_id');
            $learnerId  = $this->params('learner_id');
            $activityId = $this->params('activity_id');
            $service    = $this->reportService();

            // create the filter
            $filter = [
                'site_id' => $siteId,
                'learner_id' => $learnerId,
                'activity_id' => $activityId
            ];

            // execute the report
            $report = $service->report($filter);
            $report = current($report);

            // get the template to use
            $template = null;

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'individual-learning';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $learnerId . '-' . $activityId;
            $filename = $storagePath . '.pdf';

            // create the view model and do not show the layout
            $view = new \Savve\Pdf\View\Model\PdfModel([
                'report' => $report,
                'template' => $template,
            ]);
            $view->setOption('outputFilename', $filename);
            $view->setOption('page-size', 'A4');
            $view->setOption('orientation', 'Landscape');
            $view->setOption('margin-top', 10);
            $view->setOption('margin-bottom', 10);
            $view->setOption('margin-left', 10);
            $view->setOption('margin-right', 10);
            $view->setOption('dpi', 1200);
            $view->setTerminal(true);
            return $view;
        }
        catch (\Exception $e) {
            // failed
            throw $e;
        }
    }

    /**
     * Print to screen certificate
     */
    public function printAction ()
    {
        try {
            $siteId     = $this->params('site_id');
            $learnerId  = $this->params('learner_id');
	        $activityId = $this->params('activity_id');
            $planId     = false;
	        $service    = $this->reportService();

            $context = $this->params()->fromQuery('context');

            if (isset($context) && $context == 'plan') {
                $planId     = $activityId;
                $activityId = false;
            }

	        // create the filter
	        $filter = [
                'site_id' => $siteId,
                'learner_id' => $learnerId,
                'activity_id' => $activityId,
                'plan_id' => $planId
            ];

	        // execute the report
	        $report = $service->report($filter);
	        $report = current($report);
            if (isset($context) && $context == 'plan') {
                $report['context'] = 'plan';
            }

	        // get the template to use
	        $template = null;

	        // create the view model and do not show the layout
	        $view = new \Zend\View\Model\ViewModel([
                'report' => $report,
                'template' => $template
            ]);
	        $view->setTerminal(true);
	        return $view;
        }
        catch (\Exception $e) {
            // failed
            throw $e;
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\MyLearning\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\MyLearning\Service');
    }
}