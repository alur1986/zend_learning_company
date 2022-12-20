<?php

namespace Report\EventProgressSummary\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class TemplateController extends AbstractActionController
{

    /**
     * Display ALL tempaltes
     */
    public function directoryAction ()
    {
        $templates = $this->service('Report\EventProgressSummary\Templates');

        $message = false;
        // if a template has been deleted
        $deleteEPSTemplate = false;
        if (isset($this->getRequest()->getCookie()->deleteEPSTemplate)) {
            $deleteEPSTemplate = $this->getRequest()->getCookie()->deleteEPSTemplate;
        }
        if ($deleteEPSTemplate) {
            $message['success'] = sprintf($this->translate('Successfully deleted the template "%s"'), $deleteEPSTemplate);
        }
        // if a template has NOT been deleted
        $deleteEPSTemplateError = false;
        if (isset($this->getRequest()->getCookie()->deleteEPSTemplateError)) {
            $deleteEPSTemplateError = $this->getRequest()->getCookie()->deleteEPSTemplateError;
        }
        if ($deleteEPSTemplateError == 1) {
            $message['error'] = $this->translate('Cannot delete the template. An internal error has occurred. Please contact your support administrator or try again later.');
        }

        return [
            'templates' => $templates,
            'message'   => $message
         ];
    }

    /**
     * Create ONE template
     */
    public function createAction ()
    {
        $siteId   = $this->params('site_id');
        $service  = $this->reportTemplateService();
        $request  = $this->getRequest();
        $message  = false;

        // form
        $form = $this->form('Report\EventProgressSummary\Form\TemplateCreate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                $data = Stdlib\ObjectUtils::extract($data);
                $data['type'] = 'report-event-progress-summary';

                // create template in repository
                $template = $service->createTemplate($data, $siteId);

                // success
          //      $cookie = new SetCookie('newEPSTemplate', $template['title'], time() + 60 * 1, '/');
          //      $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully created the template "%s"'), $template['title']));
                }
                return $this->redirect()->toRoute('report/event-progress-summary/template/update', ['template_id' => $template['template_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create the template. An internal error has occurred. Please contact your support administrator or try again later.');
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
     * Update ONE template
     */
    public function updateAction ()
    {
        $templateId = $this->params('template_id');
        $service    = $this->reportTemplateService();
        $template   = $service->findOneTemplateById($templateId);
        $request    = $this->getRequest();
        $message    = false;

        // form
        $form = $this->form('Report\EventProgressSummary\Form\TemplateUpdate');

        // if a new template has been created
        $newEPSTemplate = false;
        if (isset($this->getRequest()->getCookie()->newEPSTemplate)) {
            $newEPSTemplate = $this->getRequest()->getCookie()->newEPSTemplate;
        }
        if ($newEPSTemplate) {
            $message['success'] = sprintf($this->translate('Successfully created the template "%s"'), $newEPSTemplate);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // update template in repository
                $template = $service->updateTemplate($data);

                // success
                $message['success'] = sprintf($this->translate('Successfully updated the template "%s"'), $template['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot update the template. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($template);

        return [
            'form'     => $form,
            'template' => $template,
            'message'  => $message
        ];
    }

    /**
     * Display ONE template
     */
    public function readAction ()
    {
        $templateId = $this->params('template_id');
        $service = $this->reportTemplateService();
        $template = $service->findOneTemplateById($templateId);

        return [
            'template' => $template
        ];
    }

    /**
     * Delete ONE template
     */
    public function deleteAction ()
    {
        $templateId = $this->params('template_id');
        $service = $this->reportTemplateService();
        $template = $service->findOneTemplateById($templateId);
        $request    = $this->getRequest();

        // process request
        if ($this->params('confirm')) {
            try {
                // delete template
                $service->deleteTemplate($templateId);

                // success
                $cookie = new SetCookie('deleteEPSTemplate', $template['title'], time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Successfully deleted the template "%s"'), $template['title']));
                }
                return $this->redirect()->toRoute('report/event-progress-summary/template/directory');
            }
            catch (\Exception $e) {
                // failed
                $cookie = new SetCookie('deleteEPSTemplateError', 1, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($this->translate('Cannot delete the template. An internal error has occurred. Please contact your support administrator or try again later.'));
                }
                return $this->redirect()->toRoute('report/event-progress-summary/template/directory');
            }
        }

        return [
            'template' => $template
        ];
    }

    /**
     * Get the Report Template service
     *
     * @return \Report\Service\TemplateService
     */
    public function reportTemplateService ()
    {
        return $this->service('Report\TemplateService');
    }
}