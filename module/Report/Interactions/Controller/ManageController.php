<?php

namespace Report\Interactions\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie;

class ManageController extends AbstractActionController
{

    public function indexAction ()
    {
        $request = $this->getRequest();
        $model = [];
        $form = $this->form('Report\Interactions\Form\IndexForm');
        if ($post = $this->post(false)) {
            $form->setData($post);
            if ($form->isValid()) {              
                return $this->downloadReport($form->getData());
            } else {
                $model['message'] = $form->getMessages();
            }
        }
        $model['form'] = $form;
        return $model;
    }

    public function xapiInteractionsAction () {
        try {
            $siteId = $this->params('site_id');

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            /* @var $service \Report\Interactions\Service\ReportService */
            $service = $this->reportService();

            // get all current tincan activities
            $activities = $service->getAllActivities($siteId);

            // get active learners
            $learners   = $this->service('Learner\Active');

            $reports = $service->generateReport($activities, $learners, $optionService);
            return $reports;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function csvAction ()
    {
        try {
            $siteId = $this->params('site_id');

            /* @var $optionService \Tincan\Service\OptionsService */
            $optionService = $this->service('Tincan\Options');

            /* @var $service \Report\Interactions\Service\ReportService */
            $service = $this->reportService();

            // get all current tincan activities
            $activities = $service->getAllActivities($siteId);

            // get active learners
            $learners   = $this->service('Learner\Active');

            $reports = $service->generateReport($activities, $learners, $optionService);
            
            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'inteaction-reports';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-xapi-interactions-' . time();
            $filename = $storagePath . '.csv';
            
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('reports', $reports);
            $model->setOption('outputFilename', $filename);
            return $model;
        }
        catch (\Exception $e) {
            $cookie = new SetCookie('displayEPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/xapi/interactions');
        }
    }


    /**
     * Download Interactions report
     */
    private function downloadReport ($formData)
    {
        $siteId    = $this->params('site_id');
        $request   = $this->getRequest();
        try {
            
            $filter = ['site_id' => $siteId];
            $timestamp = strtotime($formData['generate_report_at']);
            $currentDate = new \DateTime();
            $endDate = date('Y-m-t 23:59:59', $timestamp);
            $filter['generate_from'] = date('Y-m-01 00:00:00', $timestamp);
            $filter['generate_to']  = $currentDate <= new \DateTime($endDate) ? $currentDate->format('Y-m-d H:i:s') : $endDate;

            // get the current selected template
            $templateService = $this->templateService();
            $template = $templateService->findOneTemplateBySlug('default', $siteId, 'report-interactions');

            // execute the report
            $service   = $this->reportService();
            $report = $service->report($filter);

            /* @var $options \Report\Service\Options */
            $options = $this->service('Report\Options');
            $directoryPath = $options->getDirectoryPath() . DIRECTORY_SEPARATOR . 'inteaction-reports';
            Stdlib\FileUtils::makeDirectory($directoryPath);
            $storagePath = $directoryPath . DIRECTORY_SEPARATOR . 'report-interactions-' . strtolower(date('M', $timestamp));
            $filename = $storagePath . '.csv';

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('report', $report);
            $model->setVariable('template', $template);
            $model->setVariable('identifier', $this->identifierInfo());
            $model->setOption('outputFilename', $filename);

            return $model;
        } catch (\Exception $e) {
            $cookie = new SetCookie('displayEPDError', 1, time() + 60 * 1, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot process the report. An internal error has occurred. Please contact your support administrator or try again later.'));
            }
            return $this->redirect()->toRoute('report/interactions');
        }
    }

    private function identifierInfo()
    {
        return [
            '58d30cf362f2b' => 'The course content is understandable.',
            '58d30cf366be3' => 'The course flow is easy to follow.',
            '58d30cf368a3e' => 'The course content prepares me well for work.',
            '58d30cf36a6d6' => 'Sufficient time is allowed for each module.',
            '58d30cf36c859' => 'The system simulations are engaging and dynamic.',
            '58d30cf36e955' => 'The system simulations help you to understand how to use the system efficiently.',
            '58d30cf370860' => 'The voice overs are clear and relevant.',
            '58d30cf3726de' => 'The activities relate to the course content.',
            '58d30cf3746e2' => 'The feedback responses are helpful.',
            '58d30cf3767e7' => 'The interface is easy to navigate.',
            '58d30cf3785bb' => 'The look and feel of the module is visually appealing.',
            '58d30cf37a5d2' => 'Would you like to be contacted about your comments?',
            '58d44691b0c9d' => 'The course content is understandable.',
            '58d44691b33ac' => 'The course flow is easy to follow.',
            '58d44691b68c3' => 'The course content prepares me well for work',
            '58d44691b976f' => 'Sufficient time is allowed for each module',
            '58d44691bc14c' => 'The system simulations are engaging and dynamic',
            '58d44691be4f5' => 'The system simulations help you to understand how to use the system efficiently',
            '58d44691c0578' => 'The voice overs are clear and relevant',
            '58d44691c26e6' => 'The activities relate to the course content',
            '58d44691c4852' => 'The feedback responses are helpful',
            '58d44691c6926' => 'The interface is easy to navigate',
            '58d44691ca63d' => 'The look and feel of the module is visually appealing',
            '58d44691cd7aa' => 'Would you like to be contacted about your comments?'
        ];
    }

    /**
     * Get the Report service
     *
     * @return \Report\Interactions\Service\ReportService
     */
    public function reportService ()
    {
        return $this->service('Report\Interactions\Service');
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
