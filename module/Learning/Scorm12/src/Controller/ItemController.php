<?php

namespace Scorm12\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class ItemController extends AbstractActionController
{

    /**
     * Display ALL items within the SCORM12 course
     * If items does not exists in the repository,
     * read the XML manifest files and create them
     */
    public function directoryAction ()
    {
        $activityId = $this->params('activity_id');
        $service = $this->learningService();
        $activity = $service->findOneLearningActivityById($activityId);
        $items = $activity['scorm12_items'];
        $request  = $this->getRequest();

        $message = false;
        // if a success message is passed
        $directoryMessageSuccess = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageSuccess)) {
            $directoryMessageSuccess = $this->getRequest()->getCookie()->directoryMessageSuccess;
        }
        if ($directoryMessageSuccess) {
            $message['success'] = $this->translate($directoryMessageSuccess);
        }
        // if an error message is passed
        $directoryMessageError = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageError)) {
            $directoryMessageError = $this->getRequest()->getCookie()->directoryMessageError;
        }
        if ($directoryMessageError) {
            $message['error'] = $this->translate($directoryMessageError);
        }

        // if there are no items from the repository, reload from the manifest file
        if (!count($items)) {
            try {
                /* @var $scormOptions \Scorm12\Service\OptionsService */
                $scormOptions = $this->service('Scorm12\Options');

                $siteId = $this->params('site_id');
                /* @var $siteService \Site\Service\SiteService */
                $siteService = $this->service('Site\Service');
                $site = $siteService->findOneBySiteId($siteId);
                // we need the host for the CDN path
                $siteUrl = $site['url'];

                // CDN top level URL
                $cdnUrl = $scormOptions->getCdnUrl();
                // we need to add the 'domain' part to this.
                $hostname = $_SERVER['SERVER_NAME'];
                $arr      = explode(".", $hostname);
                $host     = array_shift($arr);
                $domain = implode(".", $arr);
                $cdnUrl = $cdnUrl . '.' . $domain;

	            $courseFilePath = $scormOptions->getCourseFilePath();
	        //    $manifestFileName = $courseFilePath . DIRECTORY_SEPARATOR . $scormOptions->getManifestFilename();

	            // read the manifest file and extract the scorm12 items
	            $manifestFileName = $courseFilePath . DIRECTORY_SEPARATOR . $scormOptions->getManifestFilename();
	            $items = $service->retrieveItemsFromManifest($manifestFileName, $activityId, $cdnUrl, $siteUrl);
            }
            catch (\Exception $e) {
                $message = sprintf($this->translate('Cannot load the items for the learning activity "%s" from the manifest file. Please upload a Scorm 1.2 compliant course.'), $activity['title']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                } else {
                    $cookie = new SetCookie('uploadMessageError', $message, time() + 20, '/');
                    $this->getResponse()->getHeaders()->addHeader($cookie);
                }
                return $this->redirect()->toRoute('learning/scorm12/file/upload', ['activity_id' => $activityId]);
            }
        }

        return [
            'activity' => $activity,
            'items'    => $items,
            'message'  => $message
        ];
    }

    /**
     * Display ONE item within the SCORM12 course
     */
    public function readAction ()
    {
        $itemId = $this->params('item_id');
        $service = $this->learningService();
        $item = $service->findOneItemById($itemId);

        return [
            'item' => $item
        ];
    }

    /**
     * Update ONE item within the SCORM12 course
     */
    public function updateAction ()
    {
        $itemId   = $this->params('item_id');
        $service  = $this->learningService();
        $item     = $service->findOneItemById($itemId);
        $request  = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Scorm12\Form\ItemUpdate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $item = $form->validate($post);

                // save in repository
                $service->updateItem($item);

                // success
                $message['success'] = $this->translate('Updated the item successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, ignore
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot update the item. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($item);

        return [
            'item'    => $item,
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Delete ONE item within the SCORM12 course
     */
    public function deleteAction ()
    {
        $itemId   = $this->params('item_id');
        $service  = $this->learningService();
        $item     = $service->findOneItemById($itemId);
        $activity = $item['activity'];
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the item
                $service->deleteItem($itemId);

                // success
                $message = $this->translate('Deleted the item successfully.');
                $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/scorm12/course/item/directory', ['activity_id' => $activity['activity_id']]);
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot delete the item. An internal error has occurred. Please contact your support administrator or try again later.');
                $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/scorm12/course/item/directory', ['activity_id' => $activity['activity_id']]);
            }
        }

        return [
            'item' => $item
        ];
    }

    /**
     * Get the Scorm12 doctrine service provider
     *
     * @return \Scorm12\Service\Scorm12Service
     */
    protected function learningService ()
    {
        return $this->service('Scorm12\Service');
    }
}