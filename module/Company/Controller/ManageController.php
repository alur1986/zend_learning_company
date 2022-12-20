<?php

namespace Company\Controller;

use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class ManageController extends AbstractActionController
{

    /**
     * Display all companies
     */
    public function directoryAction ()
    {
        $companies = $this->service('Company\Data');

        return [
            'companies' => $companies
        ];
    }

    /**
     * Create a new company
     */
    public function createAction ()
    {
        // form
        $form = $this->form('Company\Form\New');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $company = $form->validate($post);

                // save the company in repository
                $service = $this->companyService();
                $service->create($company);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('The company has been created successfully.')));
                return $this->redirect()->toRoute('company/update', ['company_id' => $company['company_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create the new company. An internal error has occurred. Please contact the system administrator for assistance.')));
                return $this->redirect()->refresh();
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * View a single company details
     */
    public function readAction ()
    {
        $companyId = $this->params('company_id');
        $service = $this->companyService();
        $company = $service->findOneByCompanyId($companyId);

        return [
            'company' => $company
        ];
    }

    /**
     * Update a single company
     */
    public function updateAction ()
    {
        $companyId = $this->params('company_id');
        $service = $this->companyService();
        $company = $service->findOneByCompanyId($companyId);

        // form
        $form = $this->form('Company\Form\Edit');
        $form->bind($company);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $company = $form->validate($post);

                // update company in the repository
                $service->update($company);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('The company has been updated successfully.')));
                return $this->redirect()->refresh();
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot update the company. An internal error has occurred. Please contact the system administrator for assistance.')));
                return $this->redirect()->refresh();
            }
        }

        return [
            'company' => $company,
            'form' => $form
        ];
    }

    /**
     * Get the company service
     *
     * @return \Company\Service\CompanyService
     */
    protected function companyService ()
    {
        $service = $this->service('Company\Service');
        return $service;
    }
}