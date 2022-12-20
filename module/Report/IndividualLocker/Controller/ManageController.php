<?php

namespace Report\IndividualLocker\Controller;

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
        $form = $this->form('Report\IndividualLocker\Form\Categories');
        $form->get('category_id')
            ->setValue($categoryIds);

        // if an error occurred
        $message = false;
        $displayILOError = false;
        if (isset($this->getRequest()->getCookie()->displayILOError)) {
            $displayILOError = $this->getRequest()->getCookie()->displayILOError;
        }
        if ($displayILOError == 1) {
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
                return $this->redirect()->toRoute('report/individual-locker/groups', ['session_id' => $sessionId]);
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
        $groupIds  = $session['group_id'] ?  : [];
        $request   = $this->getRequest();
        $message   = false;

        // form
        $form = $this->form('Report\IndividualLocker\Form\Groups');
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
                // only loadup groups administered by this learner
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
                return $this->redirect()->toRoute('report/individual-locker/learners', ['session_id' => $sessionId]);
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
            'options' => $output,
            'session' => $session
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
        $request    = $this->getRequest();
        $message    = false;

        // form
        $form = $this->form('Report\IndividualLocker\Form\Learners');
   //     $form->get('learner_id')->setValue($learnerIds);

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

            // get learnerrs
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
                return $this->redirect()->toRoute('report/individual-locker/range', ['session_id' => $sessionId]);
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
        $form = $this->form('Report\IndividualLocker\Form\Range');
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
                return $this->redirect()->toRoute('report/individual-locker/report', ['session_id' => $sessionId]);
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
        $sessionId = $this->params('session_id');
        $message   = false;

        // if an error occurred
        $displayILOError = false;
        if (isset($this->getRequest()->getCookie()->displayILOError)) {
            $displayILOError = $this->getRequest()->getCookie()->displayILOError;
        }
        if ($displayILOError == 1) {
            $message['error'] = $this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        try {
            $siteId    = $this->params('site_id');
            $service   = $this->reportService();
            $session = $this->session($sessionId);
            $session['site_id'] = $siteId;

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
            $templates = $this->service('Report\IndividualLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-individual-locker');

            return [
                'report' => $report,
                'templates' => $templates,
                'template' => $template,
                'filter' => $filter,
                'session_id' => $sessionId,
                'message' => $message
            ];
        }
        catch (\Exception $e) {
            // failed
            $cookie = new SetCookie('displayILOError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addSuccessMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/individual-locker/categories', ['session_id' => $sessionId]);
        }
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
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the report templates
            $templates = $this->service('Report\IndividualLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-individual-locker');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'individual-locker';
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
            return $this->redirect()->toRoute('report/individual-locker/report', ['session_id' => $sessionId]);
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
            $filter = $session->getArrayCopy();

            // execute the report
            $report = $service->report($filter);

            // get the report templates
            $templates = $this->service('Report\IndividualLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-individual-locker');

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'individual-locker';
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
            return $this->redirect()->toRoute('report/individual-locker/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Print to screen
     */
    public function printAction ()
    {
        $request   = $this->getRequest();
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
            $templates = $this->service('Report\IndividualLocker\Templates');

            // get the current selected template
            $templateService = $this->templateService();
            $templateId = $session['template_id'] ?  : null;
            $template = $templateId ? $templateService->findOneTemplateById($templateId) : $templateService->findOneTemplateBySlug('default', $siteId, 'report-individual-locker');

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
            $cookie = new SetCookie('displayILOError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/individual-locker/report', ['session_id' => $sessionId]);
        }
    }

    /**
     * Get the Report service
     *
     * @return \Report\IndividualLocker\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\IndividualLocker\Service');
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