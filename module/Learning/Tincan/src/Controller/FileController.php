<?php

namespace Tincan\Controller;

use Licence\Company\Factory\LicenceFactory;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Savve\Stdlib\FileUtils;
use Site\Event\Event as SiteEvent;

class FileController extends AbstractActionController
{

    /**
     * Display ALL files in the Tincan learning activity
     * Upload ONE file to the Tincan learning activity
     */
    public function uploadAction ()
    {
        $activityId = $this->params('activity_id');
        $service    = $this->learningService();
        $activity   = $service->findOneLearningActivityById($activityId);
        $items      = isset($activity['tincan_items']) ? $activity['tincan_items'] : false;
        $siteId     = $this->params('site_id');
        $request    = $this->getRequest();
        $message    = false;

        // if an error message is passed
        $uploadMessageError = false;
        if (isset($this->getRequest()->getCookie()->uploadMessageError)) {
            $uploadMessageError = $this->getRequest()->getCookie()->uploadMessageError;
        }
        if ($uploadMessageError) {
            $message['error'] = $this->translate($uploadMessageError);
        }

        /* @var $licence LicenceFactory */
        $licence        = $this->service("Licence\Company\Current");
        $storageSpace = $licence['storage_space'];
        $usedStorageSpace = $service->checkFilespaceUsage($siteId);
        $availableStorageSpace = ($storageSpace - $usedStorageSpace);
        if ($availableStorageSpace <= 0) {
            throw new Exception\UploadException('There is no available storage space in your site profile. Your allowed Storage Space is: ' . FileUtils::byteString($storageSpace) . ', you have used: ' . FileUtils::byteString($usedStorageSpace) . '.  You are unable to upload any further files at this time. Please contact your Site Administrator.', SiteEvent::ERROR_CODE_MAX_REACHED);
        }

        // get module options/settings for file uploads
        /* @var $options \Learning\Service\OptionsService */
        $options = $this->service('Learning\Options');
        $fileUploadPath = $options->getFileUploadPath();

        /* @var $scormOptions \Tincan\Service\OptionsService */
        $scormOptions = $this->service('Tincan\Options');

        // form
        /**
         * @var \Tincan\Factory\Form\FileUploadFormFactory
         */
        $form = $this->form('Tincan\Form\FileUpload');
        $form->get('activity_id')->setValue($activityId);

        /* recursively copy files from DRI to DIR */
        function recurse_copy($src,$dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                        recurse_copy($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        /* recursively delete a directory */
        function rrmdir($dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file)
                    if ($file != "." && $file != "..") rrmdir("$dir/$file");
                rmdir($dir);
            }
            else if (file_exists($dir)) unlink($dir);
        }

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // if file was uploaded
                if (isset($data['file_upload']) && ($data['file_upload']['size'] !== 0 || $data['file_upload']['error'] !== 4)) {

                    /* @var $fileManager \Savve\FileManager\FileManager */
                    $fileManager = $this->service('Savve\FileManager');

                    // new upload means, overwrite everything in the courses, move the old to archive
                    $oldFilePath = $fileUploadPath . DIRECTORY_SEPARATOR . $activityId;
                    if (file_exists($oldFilePath)) {
                        // !! archiving is now disabled - we just need to ensure the old ZIP is removed
                        rrmdir($oldFilePath);
                //        $archiveFilePath = $fileUploadPath . DIRECTORY_SEPARATOR . 'archive' .DIRECTORY_SEPARATOR . $activityId . DIRECTORY_SEPARATOR . date('YmdHis');
                //        $fileManager->mkdir($archiveFilePath);
                //    	$fileManager->move($oldFilePath, $archiveFilePath, true);
                    }

                    // create the file upload path
                    $fileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $activityId;
                    $fileManager->mkdir($fileUploadPath);

                    $sourceFileName = $data['file_upload']['name'];
                    $destinationFileName = $fileUploadPath . DIRECTORY_SEPARATOR . Stdlib\StringUtils::dashed(pathinfo($sourceFileName, PATHINFO_FILENAME)) . '.' . pathinfo($sourceFileName, PATHINFO_EXTENSION);

                    // upload file
                    $fileManager->upload($sourceFileName, $destinationFileName, $data);

                    // add a htaccess file
                    $service->createHtaccessRules($fileUploadPath);

                    // read and parse the manifest file
		            $courseFilePath = $scormOptions->getCourseFilePath();

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

                    // decompress the ZIP archive file
                    $service->decompressArchive($destinationFileName, $courseFilePath);

                    $directory = opendir($courseFilePath);
                    $fileCount   = 0;
                    $isDirectory = false;
                    $lastFile    = false;
                    while(false !== ( $file = readdir($directory)) ) {
                        if (( $file != '.' ) && ( $file != '..' )) {
                            $lastFile = $file;
                            if ( is_dir($file) ) {
                               $isDirectory = true;
                            }
                            $fileCount++;
                        }
                    }

                    // if the fileCount == 1 and we have $lastFile it would indicate we have a parent directory that contains the module files
                    if ($fileCount == 1 && $lastFile != false) {
                        // - we need to copy them out of the directory
                        recurse_copy($courseFilePath . '/' . $lastFile, $courseFilePath);

                        // remove the culprit directory
                        rrmdir($courseFilePath . '/' . $lastFile);

                    }

                    // read the manifest file and extract the scorm12 items
                    $manifestFileName = $courseFilePath . DIRECTORY_SEPARATOR . $scormOptions->getManifestFilename();

                    // we need to pass the 'CDN URL, Site URL' to this method to set the CDN based item_location value
                    // this will return the name of the 'launch' file which we wil use below to add the CORS JS code
                    $items = $service->retrieveItemsFromManifest($manifestFileName, $activityId, $cdnUrl, $siteUrl);

                    // from here, we can 'copy' the Tincan course container into the CDN site path
                    $cdnPath = $scormOptions->getCdnFilePath();

                    if ($cdnPath && $siteId) {

                        if (isset($siteUrl)) {
                            $cdnPath = $cdnPath . DIRECTORY_SEPARATOR . $siteUrl . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . $activityId . DIRECTORY_SEPARATOR . 'course';

                            // @todo:  this is RAW PHP code - !!! move into a Service later on !!
                            if (file_exists($cdnPath)) {
                                rrmdir($cdnPath);
                            }
                            $fileManager->mkdir($cdnPath);

                            // @todo: this is RAW PHP code - !!! move into a Service later on !!
                            recurse_copy($courseFilePath, $cdnPath);

                            // need to retest this method !!!
                    //       $fileManager->copy( $courseFilePath, $cdnPath, true); // according to 'Raff' this was supposed to copy DIR's??

                            // if this is an 'Elucidat' Scorm package we need to add a few lines of code to the beginning of its JS file
                            $elucidatJs = $cdnPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'elucidat-v2.0.min.js';

                            // to test for an AEC style course
                            $aecBazinga = $cdnPath . DIRECTORY_SEPARATOR . 'bazinga-framework' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bazinga.vendor.min.js';

                            // for custom HTML
                            $customScorm = $cdnPath . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'scorm' . DIRECTORY_SEPARATOR . 'scorm.js';

                            // for custom HTML type 2
                            $customScorm2 = $cdnPath . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'scorm' . DIRECTORY_SEPARATOR . 'scorm.js';

                            // lectora (Trivantis)
                            $lectora = $cdnPath . DIRECTORY_SEPARATOR . 'trivantis.js';

                            if (file_exists( $elucidatJs )) {

                                // get the file contents
                                $contents = file_get_contents($elucidatJs);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;" . $contents;

                                file_put_contents($elucidatJs, $contents);

                            } elseif (file_exists( $aecBazinga )) {

                                // get the file contents
                                $contents = file_get_contents($aecBazinga);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                                file_put_contents($aecBazinga, $contents);

                                // need this for the close-down page
                                $file =  $cdnPath . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'complete.html';
                                if (file_exists( $file )) {

                                    // get the file contents
                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);

                                }

                            } elseif (file_exists( $customScorm )) {
                                // Custom Scorm12 module - add JS to the scrom.js filer
                                // get the file contents
                                $contents = file_get_contents($customScorm);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                                file_put_contents($customScorm, $contents);

                            } elseif (file_exists( $customScorm2 )) {
                                // Custom Scorm12 module - add JS to the scorm.js file
                                // get the file contents
                                $contents = file_get_contents($customScorm2);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                                file_put_contents($customScorm2, $contents);

                            } elseif (file_exists( $lectora )) {
                                // Custom Scorm12 module - add JS to the trivantis.js file
                                // get the file contents
                                $contents = file_get_contents($lectora);
                                $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;
" . $contents;
                                file_put_contents($lectora, $contents);

                            } else {
                                // this will add the CORS JS code into all other modules into the 'launch' file - just before the closing </body> tag
                                foreach ($items as $item) {
                                    $location = $item['itemLocation'];
                                }

                            //    $location = substr($location, strrpos($location, "/")+1, strlen($location)); // !! failed to get launch-files' within folders !!
                                $location = substr($location, strpos($location, "course/")+7, strlen($location)); // gets a 'launch fle' relative the top-level of the course
                                $launchFile = $cdnPath . DIRECTORY_SEPARATOR . $location;
                                if (file_exists($launchFile)) {

                                    $contents = file_get_contents($launchFile);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($launchFile, $contents);
                                }
                                // Howtoo course
                                $file = $cdnPath . DIRECTORY_SEPARATOR . 'SCORMToXAPIFunctions.js';
                                if (file_exists($file)) {
                                    $contents = file_get_contents($file);
                                    $contents = "/*\nAdding learner id in local storage.\n*/function _qs(o,e){e||(e=decodeURIComponent(window.location.href)),o=o.replace(/[\[\]]/g,'\\$&');var r=new RegExp('[?&]'+o+'(=([^&#]*)|&|#|$)').exec(e);return r?r[2]?decodeURIComponent(r[2].replace(/\+/g,' ')):'':null}var actor=_qs('actor');if(actor)try{(actor=JSON.parse(actor)).mbox&&actor.mbox[0]&&window.localStorage&&window.localStorage.setItem('learnerId',actor.mbox[0].replace('mailto:','').trim())}catch(o){}\n".$contents;
                                    file_put_contents($file, $contents);
                                }

                                // for Articulate -> lms/blank.html && lms/AICCComm.html && lms/lms.js
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'lms/blank.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'lms/AICCComm.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'lms/lms.js';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;" . $contents;

                                    file_put_contents($file, $contents);
                                }

                                // alternative articulate (HTML5)
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'story_html5.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'story_content/blank.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }

                                // for some HTML courses -> end_popup.html
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'end_popup.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                // some 'craptivate' courses load in a single frame within a frameset - there is a js file in there we can use
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'standard.js';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = "/* this fixes the cross domain/host CORS issue - the same needs to exist in the Parents JS */
var localLocation =  new String(window.location);
localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);
localLocation = localLocation.slice(0, localLocation.indexOf('/'));
document.domain = localLocation;" . $contents;

                                    file_put_contents($file, $contents);
                                }

                                // Unknown !! Articulate !! variation has 3 alternative launch files
                                // 1)
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'amplaunch.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                // 2)
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'index_lms_flash.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                                // 3)
                                $file = $cdnPath . DIRECTORY_SEPARATOR .'index_lms_html5.html';
                                if (file_exists($file)) {

                                    $contents = file_get_contents($file);
                                    $contents = str_ireplace("<head>", "<head>\n<script>\n  var localLocation =  new String(window.location);\n localLocation = localLocation.slice(localLocation.indexOf('.')+1, localLocation.length);\n localLocation = localLocation.slice(0, localLocation.indexOf('/'));\n document.domain = localLocation;\n</script>\n", $contents);

                                    file_put_contents($file, $contents);
                                }
                            }
                            // remove the locally created course directory
                            rrmdir($courseFilePath);
                        }
                    }
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

        return [
            'activity' => $activity,
            'items'    => $items,
            'form'     => $form,
            'message'  => $message
        ];
    }

    /**
     * Download ONE file from the tincan learning activity
     */
    public function downloadAction ()
    {
        $activityId = $this->params('activity_id');
    //    $filename   = $this->params('filename');
        $service    = $this->learningService();
        $activity   = $service->findOneLearningActivityById($activityId);

        /* @var $fileManager \Savve\FileManager\FileManager */
        $fileManager = $this->service('Savve\FileManager');

        // get module options/settings for file uploads
        /* @var $options \Learning\Service\OptionsService */
        $options        = $this->service('Learning\Options');
        $fileUploadPath = $options->getFileUploadPath();
        $fileUploadPath = $fileUploadPath . DIRECTORY_SEPARATOR . $activityId;

        // check the old file upload path
        // !! the old /courses/ PATH as been retired !!
    //    $oldFileUploadPath = $options->getOldCourseFilePath();
    //    $oldFileUploadPath = $oldFileUploadPath . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . $activityId;

        // check if the learning activity upload path was created, usually this is created when you upload a scorm12 zip file
        if (!(file_exists($fileUploadPath))) { // || file_exists($oldFileUploadPath))) {
            $this->flashMessenger()->addErrorMessage(sprintf($this->translate('A Tincan (xAPI) course package needs to be uploaded. Please upload a Tincan course package.')));
            return $this->redirect()->toRoute('learning/tincan/file/upload', ['activity_id' => $activityId]);
        }

        // $fileUploadPath = file_exists($fileUploadPath) ? $fileUploadPath : $oldFileUploadPath;

        // read all the files from the file path
        $files = $fileManager->findFilesFromDirectory($fileUploadPath, '#(([^\s]+)?(\.(?i)(zip|rar|7z))$)#');
        $file  = $files ? current($files) : [];

        // download file requested
        if ($this->params('filename')) {
            $filename = $this->params('filename');

            if ($file['filename'] !== $filename) {
                throw new \Exception('Wrong file');
            }

            $filePath = realpath($file['pathname']);
	        $contentType = $file['mimetype'];

    	        // start download file
	        $response = Stdlib\FileUtils::download($filePath, null, $contentType);
	        return $response;
        }

        return [
            'activity' => $activity,
            'file' => $file
        ];
    }

    /**
     * Get the Tincan doctrine service provider
     *
     * @return \Tincan\Service\TincanService
     */
    protected function learningService ()
    {
        return $this->service('Tincan\Service');
    }
}
