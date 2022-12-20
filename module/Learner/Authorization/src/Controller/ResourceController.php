<?php

namespace Authorization\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class ResourceController extends AbstractActionController
{

    /**
     * Display ALL resources
     */
    public function directoryAction ()
    {
        $service = $this->authorizationService();
        $resources = $service->findAllResources();

        return [
            'resources' => $resources
        ];
    }

    /**
     * Get a list of all the routes from within the application
     */
    public function allRoutesAction()
    {
        $config = $this->serviceLocator->get('config');
        $routes = $config['router']['routes'];

        return [
            'routes' => $routes
        ];
    }
    /**
     * Create ONE new resource
     */
    public function createAction ()
    {
        $form = $this->form('Authorization\Resource\Form\Create');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save in repository
                $service = $this->authorizationService();
                $resource = $service->createResource($data);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully created the resource "%s"', $resource['title']));
                return $this->redirect()->toRoute('secure/resource/update', ['id' => $resource['id']]);
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot create the resource.'));
                return $this->redirect()->refresh();
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * View ONE resource
     */
    public function readAction ()
    {
        $id = $this->params('id');
        $service = $this->authorizationService();
        $resource = $service->findOneResourceById($id);

        return [
            'resource' => $resource
        ];
    }

    /**
     * Update ONE resource
     */
    public function updateAction ()
    {
        $id = $this->params('id');
        $service = $this->authorizationService();
        $resource = $service->findOneResourceById($id);

        // form
        $form = $this->form('Authorization\Resource\Form\Update');
        $form->populateValues($resource);

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save in repository
                $service = $this->authorizationService();
                $resource = $service->updateResource($data);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully updated the resource "%s"', $resource['title']));
                return $this->redirect()->refresh();
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot update the resource "%s.', $resource['title']));
                return $this->redirect()->refresh();
            }
        }

        return [
            'resource' => $resource,
            'form' => $form
        ];
    }

    /**
     * Delete ONE resource
     */
    public function deleteAction ()
    {
        $id = $this->params('id');
        $service = $this->authorizationService();
        $resource = $service->findOneResourceById($id);

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete resource
                $service->deleteResource($id);

                // success
                $this->flashMessenger()->addSuccessMessage(sprintf('Successfully deleted the resource "%s"', $resource['title']));
                return $this->redirect()->toRoute('secure/resource/directory');
            }
            catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot delete the resource "%s.', $resource['title']));
                return $this->redirect()->toRoute('secure/resource/directory');
            }
        }

        return [
            'resource' => $resource
        ];
    }

    /**
     * Get the Authorisation service provided
     *
     * @return \Authorization\Service\AuthorizationService
     */
    protected function authorizationService ()
    {
        $service = $this->service('Zend\Authorization\AuthorizationService');
        return $service;
    }
}