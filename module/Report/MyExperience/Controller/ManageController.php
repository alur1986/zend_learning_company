<?php

namespace Report\MyExperience\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

//use Zend\Http\Header\SetCookie as SetCookie;
//use Zend\Http\Header\Cookie;
//use Zend\Http\Request;
use Zend\Session\Container;

class ManageController extends AbstractActionController
{

    /**
     * Display the tincan (xAPI) learning activities
     */
    public function activitiesAction ()
    {
        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('user_id');
            $sessionId = $this->params('session_id');
            $trackingStatus = null;

            $session = new Container('savvy');

            $service = $this->reportService();

            // execute the report
            $activities = $service->getActivities($siteId, $learnerId);

         //   var_dump($session);
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'activities' => $activities,
            'session'    => $session
        ];
    }

    /**
     * Display the tincan (xAPI) learning activities
     */
    public function overviewAction ()
    {
        try {
            $siteId = $this->params('site_id');
            $learnerId = $this->params('learner_id');
            $sessionId = $this->params('session_id');
            $trackingStatus = null;

            $session = new Container('savvy');

            $service = $this->reportService();

            // execute the report
            $activities = $service->getActivities($siteId, $learnerId);

            //   var_dump($session);
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'activities' => $activities,
            'session'    => $session
        ];
    }

    /**
     * Display the aggregate report data
     */
    public function aggregateAction ()
    {
        try {
            $distributionId = $this->params('distribution_id');

            $service = $this->reportService();

            $getLearner = $this->getViewHelper('learner');
            $learner = $getLearner();

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            $session = new Container('savvy');

            // execute the report
            $statements = $service->getInteractions($distributionId, $optionService, $learner);
        }
        catch (\Exception $e) {
            throw $e;
        }

        //    $activities = array("EXAMINATION","INVESTIGATION/PATHOLOGY","INVESTIGATION/DIAGNOSTICS","INVESTIGATION/SPECIALIST");

        return [
            'statements'     => $statements,
            'distributionId' => $distributionId,
            'learner'        => $learner,
            'optionService'  => $optionService,
            'session' => $session
        ];
    }

    /**
     * Display the aggregate report data
     */
    public function aggregateAdminAction ()
    {
     //   die("you are now here as well!!");
        try {
            $distributionId = $this->params('distribution_id');
            $learnerId      = $this->params('learner_id');

            $service = $this->reportService();

            /** @var $learnerService \Learner\Service\LearnerService */
            $learnerService = $this->service('\Learner\Service');
            $learner = $learnerService->findOneLearnerByUserId($learnerId);

        //    $getLearner = $this->getViewHelper('learner');
        //    $learner = $getLearner();

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            $session = new Container('savvy');

            // execute the report
            $statements = $service->getInteractions($distributionId, $optionService, $learner);
        }
        catch (\Exception $e) {
            throw $e;
        }

        //    $activities = array("EXAMINATION","INVESTIGATION/PATHOLOGY","INVESTIGATION/DIAGNOSTICS","INVESTIGATION/SPECIALIST");

        return [
            'statements'     => $statements,
            'distributionId' => $distributionId,
            'learner'        => $learner,
            'optionService'  => $optionService,
            'session' => $session
        ];
    }

    /**
     * Display the individual report data
     */
    public function individualAction ()
    {
        try {
            $distributionId = $this->params('distribution_id');

            $service = $this->reportService();

            $getLearner = $this->getViewHelper('learner');
            $learner = $getLearner();

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            $session = new Container('savvy');

            // execute the report
            $statements = $service->getInteractions($distributionId, $optionService, $learner);
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'statements'     => $statements,
            'distributionId' => $distributionId,
            'learner'        => $learner,
            'optionService'  => $optionService,
            'session' => $session
        ];
    }

    /**
     * Display the experience report data
     */
    public function experienceAction ()
    {
        try {
            $distributionId = $this->params('distribution_id');

            $service = $this->reportService();

            $getLearner = $this->getViewHelper('learner');
            $learner = $getLearner();

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            $session = new Container('savvy');

            // execute the report
            $statements = $service->getExperiences($distributionId, $optionService, $learner);
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [
            'statements'     => $statements,
            'distributionId' => $distributionId,
            'learner'        => $learner,
            'optionService'  => $optionService,
            'session' => $session
        ];
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
}