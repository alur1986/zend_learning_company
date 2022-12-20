<?php

namespace Agent\Controller;

use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class ManageController extends AbstractActionController
{

    /**
     * Display all agents / agencies
     */
    public function directoryAction ()
    {
        $agents = $this->service('Agents\All');

        return [
            'agents' => $agents
        ];
    }

    /**
     * Create a new agent
     */
    public function createAction ()
    {
        $request = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Agent\Form\New');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $agent = $form->validate($post);

                // save the agent in repository
                $service = $this->agentService();
                $service->create($agent);

                // success
                $message['success'] = sprintf($this->translate('The agent/agency has been created successfully.'));
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->toRoute('agent/update', ['agent_id' => $agent['agent_id']]);
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = sprintf('Cannot create the new agent. An internal error has occurred. %s Please contact the system administrator for assistance.', $e->getMessage());
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'message' => $message,
            'form'    => $form
        ];
    }

    /**
     * View a single agent details
     */
    public function readAction ()
    {
        $agentId = $this->params('agent_id');
        $service = $this->agentService();
        $agent = $service->findOneByAgentId($agentId);

        return [
            'agent' => $agent
        ];
    }

    /**
     * Update a single agent
     */
    public function updateAction ()
    {
        $agentId = $this->params('agent_id');
        $service = $this->agentService();
        $agent   = $service->findOneByAgentId($agentId);
        $request = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Agent\Form\Edit');
        $form->bind($agent);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $agent = $form->validate($post);

                // update company in the repository
                $service->update($agent);

                // success
                $message['success'] = sprintf($this->translate('The agent has been updated successfully.'));
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = sprintf('Cannot update the agent. An internal error has occurred. %s Please contact the system administrator for assistance.', $e->getMessage());
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'message' => $message,
            'agent'   => $agent,
            'form'    => $form
        ];
    }

    /**
     * Delete ONE agent
     */
    public function deleteAction ()
    {
        $agentId = $this->params('agent_id');

        // get the current group
        $service = $this->agentService();
        $agent = $service->findOneByAgentId($agentId);

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // remove from repository
                $service->delete($agent);

                // success
                $this->flashMessenger()->addSuccessMessage($this->translate('The agent has been deleted successfully.'));
                return $this->redirect()->toRoute('agent');
            }
            catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot delete the agent "%s". An internal error has occurred: %s Please contact your support administrator or try again later.'), $agent['name'], $e->message()));
                $this->redirect()->toRoute('agenty');
            }
        }

        return [
            'agent' => $agent
        ];
    }

    /**
     * Get the agent service
     *
     * @return \Agent\Service\AgentService
     */
    protected function agentService ()
    {
        $service = $this->service('Agent\Service');
        return $service;
    }
}
