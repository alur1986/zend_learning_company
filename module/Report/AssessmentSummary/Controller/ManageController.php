<?php

namespace Report\AssessmentSummary\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\PhpEnvironment\Response;

class ManageController extends AbstractActionController
{
    /**
     * Display the list of possible Activity/Event combination available for PDF download/output
     */
    public function listAction ()
    {
        $siteId = $this->params('site_id');
        $service = $this->reportService();
        $events = $service->findAllEventsBySiteId($siteId);

        return [
            'events' => $events
        ];
    }

    /**
     * Display the learners
     */
    public function learnersAction ()
    {
        // event ID and activity ID are passed from the 'Activity/Event listing page links'
        $eventId        = $this->params('event_id');
        $activityId     = $this->params('activity_id');
        $service = $this->reportService();
        // load/list the learners that have completed the selected event
        $learners = $service->findAllLearnersByEventId($eventId);

        // get the type of activity - OTJ or WRITTEN as I was not able to map this in the 'service' SQL
        $r = $service->getActivityType($activityId);
        $activity = $r['activityType'];

        // iterate through the 'learners' and check if the PDF already exists
        $learners_ = array();
        foreach ($learners as $learner) {
            $learnerId = $learner['learner_id'];

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $file = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'assessment-summary' . DIRECTORY_SEPARATOR . 'report-' . $learnerId . '-' . $eventId . '-' . $activityId . '.pdf';

            // use this temporary path for local testing
        //    $file = '../clients/www.savvecentral.savvecentral2.local/report/' . DIRECTORY_SEPARATOR . 'assessment-summary' . DIRECTORY_SEPARATOR . 'report-' . $learnerId . '-' . $eventId . '-' . $activityId . '.pdf';
            if (file_exists($file)) {
                $learner['pdfLink'] = 'report-' . $learnerId . '-' . $eventId . '-' . $activityId;
            } else {
                $learner['pdfLink'] = false;
            }
            $learners_[] = $learner;
        }
        return [
            'learners'      => $learners_,
            'eventId'       => $eventId,
            'activityId'    => $activityId,
            'activity'      => $activity
        ];
    }

    /**
     * Export to PDF
     */
    public function pdfAction ()
    {
        try {
            $learnerId      = $this->params('learner_id');
            $eventId        = $this->params('event_id');
            $activityId     = $this->params('activity_id');
            $type           = 'written';

            $service = $this->reportService();

            // load the basic report data
            $report = $service->report($learnerId, $eventId, $activityId);

            // get the activity title
            $r = $service->getActivityName($activityId);
            $activity = $r['title'];

            // get the event title
            $r = $service->getEventName($eventId);
            $event = $r['title'];

            // get the questions and answers
            $assessment = $service->getAssessmentData($learnerId, $eventId, $activityId);

            // get the template to use
            $template = null;

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'assessment-summary';

            // use this temporary PATH foe dev and testing locally
        //    $directoryPath = '../clients/www.savvecentral.savvecentral2.local/report' . DIRECTORY_SEPARATOR . 'assessment-summary';

            Stdlib\FileUtils::makeDirectory($directoryPath);

            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $learnerId . '-' . $eventId . '-' . $activityId;
            $filename = $storagePath . '.pdf';

            // create the view model and do not show the layout
            $view = new \Savve\Pdf\View\Model\PdfModel([
                'report'    => $report[0],
                'activity'  => $activity,
                'event'     => $event,
                'assessment' => $assessment,
                'type'      => $type,
                'template'  => $template
            ]);

            $view->setOption('outputFilename', $filename);
            $view->setOption('page-size', 'A4');
            $view->setOption('orientation', 'Portrait');
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
     * Export to PDF
     */
    public function pdfotjAction ()
    {
        try {
            $learnerId      = $this->params('learner_id');
            $eventId        = $this->params('event_id');
            $activityId     = $this->params('activity_id');
            $type           = 'written';

            $service = $this->reportService();

            // load the basic report data
            $report = $service->report($learnerId, $eventId, $activityId);

            // get the activity title
            $r = $service->getActivityName($activityId);
            $activity = $r['title'];

            // get the event title
            $r = $service->getEventName($eventId);
            $event = $r['title'];

            // get the questions and answers
            $assessment = $service->getAssessmentData($learnerId, $eventId, $activityId);

            // get the template to use
            $template = null;

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'assessment-summary';

            // use this temporary PATH foe dev and testing locally
        //    $directoryPath = '../clients/www.savvecentral.savvecentral2.local/report' . DIRECTORY_SEPARATOR . 'assessment-summary';

            Stdlib\FileUtils::makeDirectory($directoryPath);

            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-' . $learnerId . '-' . $eventId . '-' . $activityId;
            $filename = $storagePath . '.pdf';

            // create the view model and do not show the layout
            $view = new \Savve\Pdf\View\Model\PdfModel([
                'report'    => $report[0],
                'activity'  => $activity,
                'event'     => $event,
                'assessment' => $assessment,
                'type'      => $type,
                'template'  => $template,
            ]);

            $view->setOption('outputFilename', $filename);
            $view->setOption('page-size', 'A4');
            $view->setOption('orientation', 'Portrait');
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
     * Download an existing PDF
     */
    public function downloadAction ()
    {
        try {

            $pdf = $this->params('pdf_id');
            // we changed the dashes (-) to underscores (_) in the learners.phtml due to an issue with 'route not found'
            // change them back here
            $pdf = str_replace("_", "-", $pdf);
            // replace the .pdf to the file name - this also caused 'route not found' issues
            $pdf = $pdf.'.pdf';

            $filename   = false;
            $inline     = false; // inline = true loads the 'PDF' in the Browser for viewing and requires 'manual' download selection
            $options = $this->service('Report\Options');
            $filePath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'assessment-summary' . DIRECTORY_SEPARATOR . $pdf;
            // use this temporary PATH for dev and testing locally
        //    $filePath   = '../clients/www.savvecentral.savvecentral2.local/report' . DIRECTORY_SEPARATOR . 'assessment-summary' . DIRECTORY_SEPARATOR . $pdf;

            if (file_exists($filePath)) {

                $contentType = 'application/pdf';
                $disposition = $inline ? 'inline' : 'attachment';

                $filename = $filename ?  : pathinfo($filePath, PATHINFO_BASENAME);

                // if the user browser agent is Internet Explorer, change the disposition to attachment to fix the IE bug about downloading
                if (Stdlib\HttpUtils::isBrowserIE()) {
                    $disposition = 'attachment';
                }

                $content = file_get_contents($filePath);
                $response = new Response();
                $response->getHeaders()
                    ->addHeaderLine("Pragma: public")
                    ->addHeaderLine('Cache-Control: must-revalidate, post-check=0, pre-check=0')
                    ->addHeaderLine("Content-type: " . $contentType)
                    ->addHeaderLine('Content-Transfer-Encoding: binary')
                    ->addHeaderLine('Content-Length: ' . filesize($filePath))
                    ->addHeaderLine("Content-Disposition: {$disposition}; filename=\"{$filename}\"");
                $response->setContent($content);
                return $response;

            } else {
                return false;
            }

        }
        catch (\Exception $e) {
            // failed
            throw $e;
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\AssessmentSummary\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\AssessmentSummary\Service');
    }
}
