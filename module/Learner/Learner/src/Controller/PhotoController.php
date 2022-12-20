<?php

namespace Learner\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class PhotoController extends AbstractActionController
{

    /**
     * Manage the learner profile photo
     */
    public function photoAction ()
    {
        $learnerId = $this->params('user_id');
        $service   = $this->learnerService();
        $request   = $this->getRequest();
        $learner   = $service->findOneByUserId($learnerId);
        $message   = false;

        // form
        $form = $this->form('Learner\Form\Photo');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // form validation
                $uploadData = $form->validate($post);

                // step 1. upload file to the destination folder
                /* @var $options \Learner\Service\Options */
                $options = $this->service('Learner\Options');
                $uploadPath = $options->getUploadPath() . DIRECTORY_SEPARATOR . $learnerId;
                $sourceFileName = $uploadData['profile_photo']['name'];
                $destinationFilename = $uploadPath . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . md5(pathinfo($sourceFileName, PATHINFO_FILENAME)) . '.' . pathinfo($sourceFileName, PATHINFO_EXTENSION);
            //    $baseUri = $options->getBaseUri() . '/' . $learnerId . '/photos';
                $uriFileName = pathinfo($destinationFilename, PATHINFO_BASENAME);

                /* @var $fileManager \Savve\FileManager\FileManager */
                $fileManager = $this->service('Savve\FileManager');
                $fileManager->upload($sourceFileName, $destinationFilename, $uploadData);

                // step 2. store the URI filename of the photo to the database
                $learner['profile_picture'] = $uriFileName;

                // save profile photo to repository
                $service = $this->learnerService();
                $learner = $service->saveProfilePhoto($learnerId, $uriFileName);

                // success
                $message['success'] = sprintf($this->translate("Successfully updated %s's profile photo"), $learner['name']);
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
                $message['error'] = $this->translate("Sorry, cannot update learner's profile photo. An internal error has occurred. Please try again later. " . $e->getMessage());
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'message'  => $message,
            'form'    => $form,
            'learner' => $learner
        ];
    }

    /**
     * Remove the learner profile photo
     */
    public function removeAction ()
    {
        $learnerId = $this->params('user_id');
        $service = $this->learnerService();
        $learner = $service->findOneByUserId($learnerId);

        if ($this->params('confirm') === 'yes') {
            try {
                /* @var $options \Learner\Service\Options */
                $options = $this->service('Learner\Options');
            //    $uploadPath = $options->getUploadPath() . DIRECTORY_SEPARATOR . $learnerId;

                // remove the profile photo
                $learner = $service->removeProfilePhoto($learnerId);

                // success
                return $this->redirect()->toRoute('learner/photo', ['user_id' => $learnerId]);
            }
            catch (\Exception $e) {
                // failed
                return $this->redirect()->toRoute('learner/photo', ['user_id' => $learnerId]);
            }
        }

        return [
            'learner' => $learner
        ];
    }

    /**
     * Display the learner profile photo
     * @throws Exception\FileNotFoundException
     * @throws Exception
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function showAction ()
    {
        try {
            $filename = $this->params('filename');
            $learnerId = $this->params('user_id');

            /* @var $options \Learner\Service\Options */
            $options = $this->service('Learner\Options');
            $uploadPath = $options->getUploadPath() . DIRECTORY_SEPARATOR . $learnerId;
            $path = $uploadPath . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . pathinfo($filename, PATHINFO_FILENAME) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

            // check if file exists
            if (!file_exists($path)) {
                throw new Exception\FileNotFoundException(sprintf('Failed to find the file "%s"', $filename), 404, null, $path);
            }
            // file contents
            $content = file_get_contents($path);
            $fileInfo = Stdlib\FileUtils::fileInfo($path);
            $contentType = $fileInfo['mimetype'];

            // @todo do some image resizing here

            // make it download
            $response = $this->getResponse();
            $response->setContent($content);
            $headers = $response->getHeaders();
            $headers->addHeaderLine("Pragma: no-cache")
                ->addHeaderLine('Cache-Control: must-revalidate, post-check=0, pre-check=0')
                ->addHeaderLine('Content-Type', $contentType);
            return $response;
        }
        catch (\Exception $e) {
            throw $e;
            // return $this->notFoundAction();
        }
    }

    /**
     * Get the learner service
     *
     * @return \Learner\Service\LearnerService
     */
    protected function learnerService ()
    {
        return $this->service('Learner\Service');
    }
}