<?php

namespace Report\MyExperience\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class InspireReportsController extends AbstractActionController
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

    //    var_dump(__LINE__); die("you have arrived here!!");

        try {
            $siteId = $this->params('site_id');

            $sessionId   = time(); //$this->params('session_id');
      
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
       //     $groups   = $this->service('Group\ActiveGroups');

            // get the report filters
        //    $filters = $this->service('Report\MyExperience\Filters');

            // get the report templates
        //   $templates = $this->service('Report\MyExperience\Templates');
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'activities'    => $activities,
            'options'       => $learners,
        //    'groups'        => $groups,
        //    'filters'       => $filters,
        //    'templates'     => $templates,
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
        $activity  = false;

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
                    //    foreach ($session['activity_iri'] as $iri) {
                    //        $activity = $learningService->findOneLearningActivityByIri($iri);
                    //        $arr[] = $activity['activity_id'];
                    //    }
                        $session['activity_id'] = $session['activity_iri']; //$arr;

                    } else {
                        // single IRI - no array available
                    //    $activity = $learningService->findOneLearningActivityByIri($session['activity_iri']);
                        $session['activity_id'] = $post['activity_iri'];// array($activity['activity_id']);
                    }

                    /** get the activity if not set */
                    if (empty($activity)) {
                        // $activity = $learningService->findOneLearningActivityByIri( $post['activity_iri']);
                        $activity = $learningService->findOneLearningActivityById( $post['activity_iri'] );
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

                    $results = $service->generateInspireReport($post, $optionService, $siteId);

                }

                // get a report template
            //    $templateService = $this->templateService();
            //   $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('csv', $siteId, 'tincan-report');

                // save the nicely formatted array for the report CSV generation
                $array   = $service->convertReportToArray($results);
                $session['results'] = $array;

            } catch (\Exception $e) {
                throw $e;
            }
        }

        return [
            'comparisons'   => $this->getComparisons(),
            'results'       => $results,
            'activity'      => $activity,
        //    'template'      => $template,
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

        $post                 = $this->post(false);
        $post['filter_id']    = false;

        $sessionId = $this->params('session_id');

        /* @var $optionService \Tincan\Service\OptionsService */
        $optionService = $this->service('Tincan\Options');

        try {

            $siteId = $this->params('site_id');
            $service = $this->reportService();
            $session = $this->session($sessionId);

            $session['site_id'] = $siteId;

            // extract the values from the session as array
            $result = $session->getArrayCopy();

            $report = $service->generateInspireReport($post, $optionService, $siteId);

            /* @var $learningService \Tincan\Service\TincanService */
            $learningService = $this->learningService();
        //    $activity = $learningService->findOneLearningActivityByIri($post['activity_iri']);
            $activity = $learningService->findOneLearningActivityById( $post['activity_iri'] );

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = null;//$session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-myexperience');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'tincan-reports';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-aggregate-' . time();
            $filename = $storagePath . '.csv';

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('comparisons', $this->getComparisons());
            $model->setVariable('activity', $activity);
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
            return $this->redirect()->toRoute('report/myexperience/reports/inspire', ['session_id' => $sessionId]);
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

            /* @var $learningService \Tincan\Service\TincanService */
            $learningService = $this->learningService();
            $activity = false; //$learningService->findOneLearningActivityByIri($post['activity_iri']);

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
            $model->setVariable('comparisons', $this->getComparisons());
            $model->setVariable('activity', $activity);
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
     * Reusable delivery of the data comparison set required for Inspire reports
     */
    private function getComparisons() {

        return array(
            "questions" => [
                "http://www.inspirehcp.com/simulation/April-SIM1" => [
                    1 => "Do you ever have any problems remembering to take your medications",
                    2 => "How often do you urinate in a normal day",
                    3 => "Do you often feel the sudden urge to go to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you ever urinate when you cough or sneeze",
                    6 => "Do you feel any pain or discomfort when urinating",
                    7 => "Do you have any symptoms of diarrhoea or constipation",
                    8 => "How long have you had these symptoms of constipation and urinary incontinence",
                    9 => "Do you wake in the night and need to urinate",
                    10 => "Have you tried modifying your diet or changing your lifestyle to reduce how often you urinate",
                    11 => "Is there anything else I can help you with today"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM1" => [
                    1 => "How often do you urinate in a normal day",
                    2 => "Do you often feel the sudden urge to go to the bathroom",
                    3 => "Do you leak urine on the way to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you ever urinate when you cough or sneeze",
                    6 => "Do you feel any pain or discomfort when urinating",
                    7 => "Do you wake in the night and need to urinate",
                    8 => "Do you have any symptoms of diarrhoea or constipation",
                    9 => "How is your sex life",
                    10 => "Have you tried modifying your diet or changing your lifestyle to reduce how often you urinate",
                    11 => "Is there anything else I can help you with today"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM1" => [
                    1 => "Do you ever have any problems remembering to take your medications",
                    2 => "How often do you urinate in a normal day",
                    3 => "Do you often feel the sudden urge to go to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you feel any pain or discomfort when urinating",
                    6 => "Do you wake in the night and need to urinate",
                    7 => "Is there anything else I can help you with today"
                ],
                "http://www.inspirehcp.com/simulation/April-SIM2" => [
                    1 => "Do you ever have any problems remembering to take your medications",
                    2 => "How often do you urinate in a normal day",
                    3 => "Do you often feel the sudden urge to go to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you ever urinate when you cough or sneeze",
                    6 => "Do you feel any pain or discomfort when urinating",
                    7 => "Do you have any symptoms of diarrhoea or constipation",
                    8 => "How long have you had these symptoms of constipation and urinary incontinence",
                    9 => "Do you wake in the night and need to urinate",
                    10 => "Have you tried modifying your diet or changing your lifestyle to reduce how often you urinate",
                    11 => "Is there anything else I can help you with today"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM2" => [
                    1 => "How often do you urinate in a normal day",
                    2 => "Do you often feel the sudden urge to go to the bathroom",
                    3 => "Do you leak urine on the way to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you ever urinate when you cough or sneeze",
                    6 => "Do you feel any pain or discomfort when urinating",
                    7 => "Do you wake in the night and need to urinate",
                    8 => "Do you have any symptoms of diarrhoea or constipation",
                    9 => "How is your sex life",
                    10 => "Have you tried modifying your diet or changing your lifestyle to reduce how often you urinate",
                    11 => "Is there anything else I can help you with today"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM2" => [
                    1 => "Do you ever have any problems remembering to take your medications",
                    2 => "How often do you urinate in a normal day",
                    3 => "Do you often feel the sudden urge to go to the bathroom",
                    4 => "Do you ever urinate when you exercise",
                    5 => "Do you feel any pain or discomfort when urinating",
                    6 => "Do you wake in the night and need to urinate",
                    7 => "Is there anything else I can help you with today"
                ]
            ],
            "examinations" => [
                "http://www.inspirehcp.com/simulation/April-SIM1" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Breast Exam",
                    "Carotid Pulse",
                    "Cervical Exam",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Pelvic / Vaginal Exam",
                    "Popliteal Pulse",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM1" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Breast Exam",
                    "Carotid Pulse",
                    "Cervical Exam",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Pelvic / Vaginal Exam",
                    "Popliteal Pulse",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM1" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Carotid Pulse",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Chest Measurement",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Penis Exam",
                    "Popliteal Pulse",
                    "Prostate Exam",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ],
                "http://www.inspirehcp.com/simulation/April-SIM2" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Breast Exam",
                    "Carotid Pulse",
                    "Cervical Exam",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Pelvic / Vaginal Exam",
                    "Popliteal Pulse",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM2" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Breast Exam",
                    "Carotid Pulse",
                    "Cervical Exam",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Pelvic / Vaginal Exam",
                    "Popliteal Pulse",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM2" => [
                    "Arm Exam",
                    "Auscultate Abdomen",
                    "Blood Pressure",
                    "Brachial Pulse",
                    "Carotid Pulse",
                    "Check Ankle Reflex",
                    "Check Biceps Reflex",
                    "Check Knee Reflex",
                    "Check Plantar Reflex",
                    "Check Supinator Reflex",
                    "Check Triceps Reflex",
                    "Chest Measurement",
                    "Ear Canal",
                    "Ear Drum",
                    "Eye Exam",
                    "Femoral Pulse",
                    "Foot Exam",
                    "Hand Exam",
                    "Head & Neck Exam",
                    "Hearing Acuity",
                    "Height",
                    "Hip Exam",
                    "Hip Measurement",
                    "Knee Exam",
                    "Leg/Calf Exam",
                    "Listen To Chest",
                    "Listen To Heart",
                    "Nasal Exam",
                    "Oropharynx",
                    "Palpate and Percuss Liver",
                    "Palpate and Percuss Spleen",
                    "Palpate Cervical Lymph Node",
                    "Palpate Femoral Lymph Nodes",
                    "Palpate Inguinal Lymph Nodes",
                    "Palpate LLQ Abdomen",
                    "Palpate LUQ Abdomen",
                    "Palpate Popliteal Lymph Nodes",
                    "Palpate RLQ Abdomen",
                    "Palpate RUQ Abdomen",
                    "Palpate Supratrochlear Lymph Nodes",
                    "Palpate Thyroid",
                    "Palplate Axillary Lymph Nodes",
                    "Palplate Mediastinal Lymph Nodes",
                    "Palplate Supraclavicular Lymph Nodes",
                    "Pedal Pulse",
                    "Penis Exam",
                    "Popliteal Pulse",
                    "Prostate Exam",
                    "Radial Pulse",
                    "Rectal Exam",
                    "Respiratory Rate",
                    "Skin Check",
                    "Spinal Exam",
                    "Temperature",
                    "Tonsil",
                    "Visual Acuity",
                    "Waist Measurement",
                    "Weight"
                ]
            ],
            "pathology" => [
                "http://www.inspirehcp.com/simulation/April-SIM1" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Microbial screen",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM1" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM1" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Microbial screen",
                    "Prostate-specific antigen test",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ],
                "http://www.inspirehcp.com/simulation/April-SIM2" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Microbial screen",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM2" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM2" => [
                    "Basic metabolic panel",
                    "Blood clotting",
                    "Blood culture",
                    "Bone Marrow Aspirate",
                    "Comprehensive metabolic panel",
                    "Enzyme tests",
                    "Full blood count",
                    "HbA1c",
                    "Lipid panel",
                    "Liver function tests",
                    "Microbial screen",
                    "Prostate-specific antigen test",
                    "Renal function tests",
                    "Sputum culture",
                    "Stool culture",
                    "Thyroid function test",
                    "Urinalysis",
                    "Urine culture"
                ]
            ],
            "diagnostics" => [
                "http://www.inspirehcp.com/simulation/April-SIM1" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT scan",
                    "CTCA scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & Neck",
                    "Mammography",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM1" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT scan",
                    "CTCA scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & neck",
                    "Mammography",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM1" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT Scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & neck",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ],
                "http://www.inspirehcp.com/simulation/April-SIM2" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT scan",
                    "CTCA scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & Neck",
                    "Mammography",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM2" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT scan",
                    "CTCA scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & neck",
                    "Mammography",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM2" => [
                    "Abdomen & pelvis",
                    "Abdominal",
                    "Angiogram",
                    "Chest",
                    "CT Scan",
                    "DEXA scan",
                    "ECG",
                    "Echocardiogram",
                    "EEG",
                    "Endoscopy",
                    "Extremities",
                    "Fluoroscopy",
                    "Gastrointestinal tract",
                    "Head & neck",
                    "MRI scan",
                    "Musculoskeletal",
                    "PET scan",
                    "Renal & urinary tract",
                    "Respiratory tract",
                    "Spine & abdomen",
                    "Spirometry",
                    "Ultrasound",
                    "Urinary tract",
                    "Urodynamic study",
                    "X-ray"
                ]
            ],
            "specialists" => [
                "http://www.inspirehcp.com/simulation/April-SIM1" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urogynaecology",
                    "Urology",
                    "Vascular Medicine"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM1" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urogynaecology",
                    "Urology",
                    "Vascular Medicine"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM1" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urology",
                    "Vascular Medicine"
                ],
                "http://www.inspirehcp.com/simulation/April-SIM2" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urogynaecology",
                    "Urology",
                    "Vascular Medicine"
                ],
                "http://www.inspirehcp.com/simulation/Tabitha-SIM2" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urogynaecology",
                    "Urology",
                    "Vascular Medicine"
                ],
                "http://www.inspirehcp.com/simulation/Henry-SIM2" => [
                    "Cardiology",
                    "Dermatology",
                    "Dietetics",
                    "Endocrinology",
                    "Gastroenterology",
                    "Haematology",
                    "Hepatology",
                    "Immunology",
                    "Infectious Diseases",
                    "Internal Medicine",
                    "Medical Oncology",
                    "Nephrology",
                    "Neurology",
                    "Occupational Therapy",
                    "Opthalmology",
                    "Orthopaedics",
                    "Physiotherapy",
                    "Psychiatry",
                    "Psychology",
                    "Respiratory Medicine",
                    "Rheumatology",
                    "Thoracic Medicine",
                    "Urology",
                    "Vascular Medicine"
                ]
            ]
        );
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