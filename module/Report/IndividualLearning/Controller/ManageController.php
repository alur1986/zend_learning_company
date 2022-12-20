<?php

namespace Report\IndividualLearning\Controller;

  // ini_set("error_reporting", E_ALL);

  use Savve\Mvc\Controller\AbstractActionController;
  use Savve\Stdlib;
  use Savve\Stdlib\Exception;
  use Zend\Http\Header\SetCookie as SetCookie;

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
        $form = $this->form('Report\IndividualLearning\Form\Activities');
        $form->get('activity_id')
            ->setValue($activityIds);

        // if an error occurred
        $message    = false;
        $displayILEError = false;
        if (isset($this->getRequest()->getCookie()->displayILEError)){
            $displayILEError = $this->getRequest()->getCookie()->displayILEError;
        }
        if ($displayILEError == 1) {
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
                return $this->redirect()->toRoute('report/individual-learning/learners', ['session_id' => $sessionId]);
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

        // get learner
        $getLearner = $this->getViewHelper( 'Learner' );
        $learner    = $getLearner();
        $learnerId  = $learner['userId']; // send to Service for admin priviledge check

        //get the user's current role
        $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
        //Set to guest access if no user level is found
        $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;
        $initializedOptions = $form->get('activity_id')->getValueOptions();
        if ($currentUserLevel == 500) {
            // is group admin
            // we need to get a list of 'leaner ID's' based on the groups that this learner is an admin of group
            /* @var $groupService \Group\Learner\Service\GroupLearnerService */
            $groupService = $this->service('Group\Learner\Service');
            $newActivities = $groupService->getActivitiesRelatedToGroupAdmin($learnerId);
            $form->get('activity_id')->setValueOptions($newActivities);
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
        $sessionId  = $this->params('session_id');
        $session    = $this->session($sessionId);
        $learnerIds = $session['learner_id'] ?  : [];
        $activityIds = ($session['activity_id'] && is_array($session['activity_id'])) ? array_map(
            function ($v) {
                return (int)$v;
            },
            $session['activity_id']
        ) : [];

        $request    = $this->getRequest();
        $message    = false;
        $siteId     = $this->params('site_id');

        // form
        $form = $this->form('Report\IndividualLearning\Form\Learners');
   //     $form->get('learner_id')->setValue($learnerIds);

        // get learner
        $getLearner = $this->getViewHelper( 'Learner' );
        $learner    = $getLearner();
        $learnerId  = $learner['userId']; // send to Service for admin priviledge check

        //get the user's current role
        $currentUserRole = ($this->getServiceLocator()->get('Learner\LearnerRole'));
        //Set to guest access if no user level is found
        $currentUserLevel = ($currentUserRole && isset($currentUserRole['level'])) ? $currentUserRole['level']['id'] : 1;

        // ger learners
        if ($currentUserLevel == 500) {
            // is group admin
            // we need to get a list of 'leaner ID's' based on the groups that this learner is an admin of
            /* @var $groupService \Group\Learner\Service\GroupLearnerService */
            $groupService = $this->service('Group\Learner\Service');
            $learners     = $groupService->findAllLearnersByGroupAdmin($learnerId, $activityIds);

        } else {
            /* @var $learnerService \Learner\Service\LearnerService */
            $learnerService = $this->service('Learner\Service');
            $learners       = $learnerService->findAllBySiteIdWithLimit ($siteId, 0, [ 'new', 'active', 'inactive' ], $activityIds);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // store selected group IDs to the current session
                unset($session['learner_id']);
                if (isset($data['learner_id']) && !empty($data['learner_id'])) {
                    $session['learner_id'] = $data['learner_id'];
                }

                // success
                return $this->redirect()->toRoute('report/individual-learning/range', ['session_id' => $sessionId]);
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
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\IndividualLearning\Form\Range');

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
                return $this->redirect()->toRoute('report/individual-learning/report', ['session_id' => $sessionId]);
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
        $request   = $this->getRequest();
        $message   = false;
        $sessionId = $this->params('session_id');
        $reportType = $this->params()->fromQuery('report_type');

        // if an error occurred
        $displayILEError = false;
        if (isset($this->getRequest()->getCookie()->displayILEError)) {
            $displayILEError = $this->getRequest()->getCookie()->displayILEError;
        }
        if ($displayILEError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        try {
	        $siteId = $this->params('site_id');
	        $service = $this->reportService();

	        $session = $this->session($sessionId);
	        $session['site_id'] = $siteId;

	        // extract the values from the session as array
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);
        }
        catch (\Exception $e) {
            // failed
        //    throw $e;
            $cookie = new SetCookie('displayILEError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-details/activities', ['session_id' => $sessionId]);
        }

        $fName = "individual_report_".$siteId."_".date("m_d_Y_H:i");
        switch (strtolower($reportType)) {
            case 'csv':
                return $service->convertReportToCsv($report, $fName);
            case 'xlsx':
                return $service->convertReportToExcel($report, $fName);
        }

        return [
            'report'    => $report,
            'filter'    => $filter,
            'sessionId' => $sessionId,
            'message'   => $message
        ];
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
            $learnerId = $this->params('learner_id');
            $activityId = $this->params('activity_id');
            $service = $this->reportService();

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
         //   throw $e;
            $cookie = new SetCookie('displayILEError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-details/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Print to screen certificate
     */
    public function printAction ()
    {
        $request   = $this->getRequest();
        $sessionId = $this->params('session_id');
        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('learner_id');
	        $activityId = $this->params('activity_id');
	        $service = $this->reportService();

	        // create the filter
	        $filter = [
                'site_id' => $siteId,
                'learner_id' => $learnerId,
                'activity_id' => $activityId
            ];

	        // execute the report
	        $report = $service->report($filter);
	        $report = current($report);

	        // get the templat to use
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
         //   throw $e;
            $cookie = new SetCookie('displayILEError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/event-progress-details/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\IndividualLearning\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\IndividualLearning\Service');
    }
}