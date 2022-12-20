<?php

namespace Resource\Controller;

use Savve\Exception\InvalidFormException;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class FileController extends AbstractActionController
{

    /**
     * Display ALL files in the resource learning activity
     * Upload ONE file to the resource learning activity
     */
    public function directoryAction ()
    {
        $activityId = $this->params('activity_id');
        $service  = $this->resourceService();
        $request  = $this->getRequest();
        $message  = false;

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

        // form
        $form = $this->form('Resource\Form\FileUpload');
        $form->get('activity_id')->setValue($activityId);

        // process form request
        if ($post = $this->post(false)) {
            try {

                // validate form
                $data = $form->validate($post);

                if((!isset($post['url']) || empty($post['url'])) && (!isset($post['file_upload'])||empty($post['file_upload']) || $post['file_upload']['size'] == 0) ){
                    $form->setMessages(array_merge_recursive($form->getMessages(),['file_upload'=>['noResource'=>'Please upload a file or enter a valid url']]));
                    throw new Exception\InvalidFormException("Form is invalid");
                }

                /* @var $options \Learning\Service\OptionsService */
                $options = $this->service('Learning\Options');

                // create the file upload path
                $fileUploadPath = $options->getFileUploadPath() . DIRECTORY_SEPARATOR . $activityId;
                Stdlib\FileUtils::makeDirectory($fileUploadPath);

                // if file was uploaded
                if (isset($data['file_upload']) && ($data['file_upload']['size'] !== 0 || $data['file_upload']['error'] !== 4)) {
                    $sourceFileName = $data['file_upload']['name'];
                    $destinationFileName = $fileUploadPath . DIRECTORY_SEPARATOR . Stdlib\StringUtils::dashed(pathinfo($sourceFileName, PATHINFO_FILENAME)) . '.' . pathinfo($sourceFileName, PATHINFO_EXTENSION);

                    // upload file
                    /* @var $fileManager \Savve\FileManager\FileManager */
                    $fileManager = $this->service('Savve\FileManager');
                    $fileManager->upload($sourceFileName, $destinationFileName, $data);

                    // resource entity
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($destinationFileName);
                    $resource = [
                        'title' => $data['title'],
                        'filename' => pathinfo($destinationFileName, PATHINFO_BASENAME),
                        'filetype' => $mimeType,
                        'activity_id' => $activityId
                    ];

                    // save the uploaded file data to the repository
                    $service->saveResourceFile($resource, $activityId);
                }

                // if the URL was entered
                // we need to ensure that a file is not being uploaded or that a 'url' value has been persisted
                if (isset($data['url']) && !empty($data['url']) && $data['file_upload']['error'] == 4) {
                    $url = $data['url'];

                    $baseName = Stdlib\StringUtils::dashed(Stdlib\HttpUtils::stripScheme($url));
                    $baseName = trim($baseName, '/');
                    $destinationFileName = $fileUploadPath . DIRECTORY_SEPARATOR . $baseName . '.url';

                    // create the internet shortcut file
                    $service->createInternetShortcutFile($url, $destinationFileName);

                    // save the uploaded file data to the repository
                    $resource = [
                        'title' => $data['title'],
                        'filename' => pathinfo($destinationFileName, PATHINFO_BASENAME),
                        'filetype' => 'application/internet-shortcut',
                        'activity_id' => $activityId
                    ];
                    $service->saveResourceFile($resource, $activityId);
                }

                // success
                $message['success'] = $this->translate('Uploaded the file for the learning activity successfully.');
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
                $message['error'] = $this->translate('Cannot upload the file. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $activity = $service->findOneLearningActivityById($activityId);
        $files     = $activity['files'];
        $resources = $activity['resources'];
        $files     = array_map(function($item) use ($resources){
            //Update the title
            $found = $resources->filter(function ($resource) use ($item){return $item['filename'] == $resource['filename'];})->current();
            if ($item['extension'] === 'url') {
                $config = parse_ini_file($item['pathname']);
                $url = $config['URL'];
                $item['filename'] = $url;
            }
            if($found){
                $item['title'] = $found['title'];
            }
            return $item;
        }, $files);

        return [
            'activity' => $activity,
            'form'    => $form,
            'files'   => $files,
            'message' => $message
        ];
    }

    /**
     * Download ONE file from the resource learning activity
     */
    public function downloadAction ()
    {
        $activityId = $this->params('activity_id');
        $filename = $this->params('filename');
        $service = $this->resourceService();
        $directories = [];
        /* @var $options \Learning\Service\OptionsService */
        $options = $this->service('Learning\Options');

        // check the new file upload path
        $fileUploadPath = $options->getFileUploadPath() . DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($fileUploadPath)) {
            $directories[] = $fileUploadPath;
        }

        // check the old file upload path
        $oldFileUploadPath = $options->getOldCourseFilePath();
        $oldFileUploadPath = $oldFileUploadPath . DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($oldFileUploadPath)){
            $directories[] = $oldFileUploadPath;
        }

        /* @var $fileManager \Savve\FileManager\FileManager */
        $fileManager = $this->service('Savve\FileManager');
        // find the file requested in the upload path
        $files = $fileManager->findFile($filename, $directories);
        $file = current($files);
        $filePath = realpath($file['pathname']);
        $contentType = $file['mimetype'];

        // if file is a URL file, open the link
        if ($file['extension'] === 'url') {
            $config = parse_ini_file($filePath);
            $url = $config['URL'];
            return $this->redirect()->toUrl($url);
        }

        // download file
        $response = Stdlib\FileUtils::download($filePath, null, $contentType);

        return $response;
    }

    /**
     * Delete ONE file from the resource learning activity
     */
    public function deleteAction ()
    {
        $filename   = $this->params('filename');
        $activityId = $this->params('activity_id');
        $service    = $this->resourceService();
        $activity   = $service->findOneLearningActivityById($activityId);
        $request    = $this->getRequest();
        $directories = [];

        /* @var $options \Learning\Service\OptionsService */
        $options = $this->service('Learning\Options');

        // check the new file upload path
        $fileUploadPath = $options->getFileUploadPath(). DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($fileUploadPath)) {
            $directories[] = $fileUploadPath;
        }

        // check the old file upload path
        $oldFileUploadPath = $options->getOldCourseFilePath();
        $oldFileUploadPath = $oldFileUploadPath . DIRECTORY_SEPARATOR . $activityId;
        if (file_exists($oldFileUploadPath)){
            $directories[] = $oldFileUploadPath;
        }

        /* @var $fileManager \Savve\FileManager\FileManager */
        $fileManager = $this->service('Savve\FileManager');

        // find the file requested in the upload path
        $files = $fileManager->findFile($filename, $directories);
        $file = current($files);
        $filePath = realpath($file['pathname']);

    //    var_dump($filePath); die;

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                if ($filePath) {
                    if (file_exists($filePath)) {
                        // delete file
                        try {
                            unlink($filePath);
                        } catch(\Exception $e) {
                            throw $e;
                        }
                    }

                    // remove the file data from the repository
                    $service->deleteResourceFile($filename, $activityId);

                    // if the above (unlink) continues to cause a problem, might use this in an ELSE block
                    // now we need to 'remove' the file, else it will continue to shgow in the directory because of the 'subscriber' (Resource\Doctrine\Event\Subscriber) event
                    /* @var $fileManager \Savve\FileManager\FileManager *//*
                    $fileManager = $this->get('Savve\FileManager');

                    // get module options/settings for file uploads
                    /* @var $options \Learning\Service\OptionsService *//*
                    $options = $serviceLocator->get('Learning\Options');

                    // check the new file upload path
                    $fileUploadPath = $options->getFileUploadPath();
                    $fileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $activityId;
                    if (file_exists($fileUploadPath)) {
                        Stdlib\ArrayUtils::arrayPush($files, $fileManager->readFiles($fileUploadPath));
                    }*/
                }

                // success
                $message = $this->translate('Deleted the file from the learning activity successfully.');
            //    $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
            //    $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/resource/file/directory', ['activity_id' => $activity['activity_id']]);
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot delete the file. An internal error has occurred. Please contact your support administrator or try again later.');
            //    $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
            //    $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/resource/file/directory', ['activity_id' => $activity['activity_id']]);
            }
        }

        return [
            'activity' => $activity,
            'file' => $file
        ];
    }

    /**
     * Get the Resource doctrine service provider
     *
     * @return \Resource\Service\ResourceService
     */
    protected function resourceService ()
    {
        return $this->service('Resource\Service');
    }
}