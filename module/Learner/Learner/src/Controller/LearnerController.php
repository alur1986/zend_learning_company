<?php

namespace Learner\Controller;

use Savvecentral\Entity;
use Learner\Event\Event as LearnerEvent;
use Learner\Exception as LearnerException;
use Savve\Mvc\Controller\AbstractActionController;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\View\Model\ViewModel;
use Savve\Controller\AbstractController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;


class LearnerController extends AbstractActionController
{

    /**
     * Display the learner directory
     */
    public function directoryAction ()
    {
        $request  = $this->getRequest();
        $learners = false;

        // only run this if the request is not coming via AJAX
    //    if (!$request->isXmlHttpRequest()) {
    //        $learners = $this->service('Learner\All');
    //    }

        return [
            'learners' => $learners
        ];
    }

    /**
     * Register learner
     */
    public function registerAction ()
    {
        $siteId = $this->params('site_id');

        /* @var $service \Learner\Service\LearnerService */
        $service  = $this->service('Learner\Service');
        $request  = $this->getRequest();

        // get and set the 'Site' helper
        $Site = $this->getViewHelper( 'Site' );
        $site = $Site();

        // always starts null
        $activities = null;
        $startDate  = null;

        // form
        $form = $this->form('Learner\Form\Register');

        // Regardless of the site we need to ensure its not exceeding it user license
        // There is an EventManager Listener in 'Licence Company' but its better suited to administrative usage as the error response is haphazard
        // Lets get the license value for 'num_learners' first
        /* $var $licenseService \Savvecentral\Entity\LicenceCompany */
        $licenseService = $this->service('Licence\Company\Service');
        $numLearners    = $licenseService->findLearnerLimitBySiteId ($siteId);
        // check if we have previously set an error
        $licenceFailed = 0;
        if (isset($this->getRequest()->getCookie()->licenceFailed)) {
            $licenceFailed = $this->getRequest()->getCookie()->licenceFailed;
        }

        // get the number of active learners for the site
        $learners = $this->service('Learner\Active');

        if (isset($numLearners['numlearners']) && count($learners) >= $numLearners['numlearners'] && $licenceFailed != count($learners)) {
            // license limit will be exceeded - return error
            $this->flashMessenger()->addInfoMessage($this->translate('The company licence has reached the maximum number of learners that can be registered. Please contact an administrator.'));
            $cookie = new SetCookie('licenceFailed', count($learners), time() + 30, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
            return $this->redirect()->refresh();
        }

        $groupService = $this->service('Group\Service');
        $groups = $groupService->findAllActiveGroupsBySiteId($siteId) ?  : new ArrayCollection();

        $valueOptions = [];
        foreach ($groups as $group) {
            $valueOptions[] = array_merge([
                'label' => $group['name'],
                'value' => $group['group_id']
            ], Stdlib\ObjectUtils::extract($group));
        }

        $form->get('group_id')->setValueOptions($valueOptions);

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $prefix = 'https://';
        } else {
            $prefix = 'http://';
        }

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                // if its the 'Simple Rego' form we need to duplicate the password that has been entered so we don't fail the validation
                if ($site['simple_registration'] == true) {
                    $post['confirm_password'] = $post['new_password'];
                }
                $data = $form->validate($post);
                $data['site_id'] = $siteId;

                // if agent/agency is enable - check here and return immediately if is invalid
                if ($site['show_agents'] == true) {
                    $code     = $data['agent_code'];
                    $password = $data['agent_password'];

                    /** @var $agentService \Agent\Service\AgentService */
                    $agentService = $this->service('Agent/Service');

                    $agent        = $agentService->findOneByAgentSite($site['site_id'], $code, $password);
                    $agencyName   = $agent['name'];
                    $dbCode       = $agent['code'];
                    $dbPassword   = $agent['password'];
                    if (!isset($agent) || count($agent) == 0 || $code !== $dbCode || $password !== $dbPassword) {
                        $this->flashMessenger()->addErrorMessage($this->translate('Cannot register your details. The agent/agency details you entered are incorrect.'));
                        return $this->redirect()->refresh();
                    }
                }

               	// register and save
            //    $learner = false;
               	$learner = $service->register($data);

                if ($site['show_agents'] == true) {
                    // for Tramada we want to handle auto distribution separately / individually
                    $startDate  = $data['start_date'];
                    $planId     = $data['course_selector'];
                    $agentEmail = $data['agent_email'];

                    // auto distribute bases on site and or course selected
                    /* @var $activityService \Learning\Service\LearningService */
                    $activityService = $this->service('Learning\Service');
                    $activities = $activityService->getAutoDistributeCourseOnRegister($planId, $siteId);

                } else {
                    // regardless of the action after registration, we will check for any 'Auto Distribute' on Registration activities
                    /* @var $activityService \Learning\Service\LearningService */
                    $activityService = $this->service('Learning\Service');
                    $activities = $activityService->getAutoDistributeOnRegister($siteId);
                }

                if (count($activities)) {
                    // distribute these activities to the new user/learner
                    /** @var $distributionService \Distribution\Learning\Service\LearningDistributionService */
                    $distributionService = $this->service('Distribution\Learning');
                    $distributed = $distributionService->autoDistributeActivities($activities, $learner, 'registration', $startDate);

                    if ($site['show_agents'] == true) {

                        // only run for Tramada
                        if ($siteId == 200108 || 200123) {
                            /** @var $learningPlanService \LearningPlan\Service\LearningPlan */
                            $learningPlanService = $this->service('LearningPlan\Service');
                            $course = $learningPlanService->findOneLearningPlanById( $planId );
                            $course['start_date'] = $startDate;

                            /* @var $twig \Savve\Twig\Twig */
                            $twig = $this->service('Savve\Twig');

                            /* @var $mail \Email\Service\Email */
                            $mail = $this->service('Email\Service\Email');

                            /* @var $templateService \Email\Template\Service\TemplateService */
                            $templateService = $this->service('Email\Template\Service');

                            $distributionService->notifyCourseDistributionByAgent( $twig, $mail, $templateService, $site, $agencyName, $agentEmail, $data['mobile_number'], $learner, $course );
                        }
                    }
                }

                // success
            //    $cookie = new SetCookie('newLearner', $data['first_name'] . ' ' . $data['last_name'], time() + 30, '/');
            //    $this->getResponse()->getHeaders()->addHeader($cookie);
                // for 'Auto Login' we run this block
                if ($site['auto_login'] == true) {
                    if (!$request->isXmlHttpRequest()) {
                        // currently this should always run - not using Ajax to post this
                        $this->flashMessenger()->addSuccessMessage($this->translate('You have successfully registered and have been automatically redirected to your dashboard.'));
                    }
                    // get the new learner 'impersonate' hash string
                    $key = $service->getLearnerImpersonateKey( $learner['userId'] );
                    // check if 'remember me' is enabled
                    if ($site['remember_me'] == true) {
                        // save the 'key' into a cookie (to be eaten later)
                        $chookie = new SetCookie('rememberMe', $key['authenticationToken'], time() + 60 * 60 * 24 * 30, '/');
                        $this->getResponse()->getHeaders()->addHeader($chookie);
                    }
                    $route = $prefix . $_SERVER['SERVER_NAME'] . '/learner/impersonate/'.$key['authenticationToken'];
                    return $this->redirect()->toUrl($route);

                } else {
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($this->translate('You have successfully registered. Please login using your email, mobile number or employment ID, and the password that you just created.'));
                    }
                    return $this->redirect()->toRoute('login');
                }
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing? depends!!
                // for Inspire and friends
                if (isset($post['group_id']) && strlen($post['group_id'])) {
                    $route = $prefix . $_SERVER['SERVER_NAME'] . '/register?group='.$post['group_id'];
                    return $this->redirect()->toUrl($route);
                }
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage($this->translate('Cannot register your details. An internal error has occurred: ' . $e->getMessage() . ' Please try again later.'));
                return $this->redirect()->refresh();
            }
        }

        return [
            'form' => $form
        ];
    }

    /**
     * Create NEW learner
     */
    public function createAction ()
    {
        $siteId = $this->params('site_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $request  = $this->getRequest();

        // form
        $form = $this->form('Learner\Form\Create');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                $data['site_id'] = $siteId;

                // create learner
                $learner = $service->create($data);

                // success
                $cookie = new SetCookie('newLearner', $learner['name'], time() + 20, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage(sprintf("%s's account has been successfully created.", $learner['name']));
                }
                return $this->redirect()->toRoute('learner/update', ['user_id' => $learner['user_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf('Cannot create a new learner. An internal error has occurred: %s Please try again later.', $e->getMessage()));
                return $this->redirect()->refresh();
            }
        }
        return [
            'form' => $form
        ];
    }

    /**
     * View learner details
     */
    public function readAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $learner = $service->findOneByUserId($userId);
        return [
            'learner' => $learner
        ];
    }

    /**
     * Update learner details
     */
    public function updateAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $learner = $service->findOneLearnerByUserId($userId);
        $request  = $this->getRequest();

        // form
        $form = $this->form('Learner\Form\Update');
        $message    = false;
        $newLearner = false;
        if (isset($this->getRequest()->getCookie()->newLearner)) {
            $newLearner = $this->getRequest()->getCookie()->newLearner;
        }
        if ($newLearner) {
            $message['success'] = sprintf("%s's " . $this->translate("account has been successfully created."), $newLearner);
            $cookie = new SetCookie('newLearner', false, time() - 120, '/');
            $this->getResponse()->getHeaders()->addHeader($cookie);
        }
        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $learner = $form->validate($post);

                // save learner
                $learner = $service->update($learner);

                // success!
                $message['success'] = sprintf($this->getTranslatedMessage("%s's details were successfully updated."), $learner['name']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
                $form->populateValues(Stdlib\ObjectUtils::extract($learner));
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation failure
            }
            catch (\Exception $e) {
                // fail!
                $message['error'] = $this->translate('Cannot update the learner details. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        } else {
            $learner['employmentId'] = $learner['employment']['employmentId'];
            $form->populateValues(Stdlib\ObjectUtils::extract($learner));
        }

        return [
            'message' => $message,
            'learner' => $learner,
            'form'    => $form
        ];
    }

    /**
     * Change password
     */
    public function passwordAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $request = $this->getRequest();
        $message = false;
        $learner = $service->findOneByUserId($userId);

        // form
        $form = $this->form('Learner\Form\Password');
        $form->get('user_id')->setValue($userId);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // update the password
                $service->changePassword($userId, $data['new_password']);

                // success
                $message['success'] = sprintf($this->getTranslatedMessage("%s's password was successfully changed."), $learner['name']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot change password. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'message' => $message,
            'form'    => $form,
            'learner' => $learner
        ];
    }

    /**
     * Reset password
     */
    public function resetPasswordAction ()
    {
        $passwordToken = $this->params('password_token');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $request = $this->getRequest();
        $message = false;
        $learner = $service->findOneByPasswordToken($passwordToken);

        // if no password token or learner not found
        if (!$learner) {
            $this->flashMessenger()->addErrorMessage(sprintf("Cannot reset password. The link is either invalid or request has expired. Please make a new reset password request"));
            return $this->redirect()->toRoute('learner/forgot-password');
        }

        // form
        $form = $this->form('Learner\Form\ResetPassword');
        $form->get('password_token')->setValue($passwordToken);

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // change password and save
                $learnerId = $learner['user_id'];
                $learner  = $service->resetPassword($learnerId, $data['new_password']);

                // success
                $cookie = new SetCookie('newPassword', $learner['name'], time() + 30, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                $message['success'] = $this->translate("Your password has been successfully changed.");
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->toRoute('login', ['identity' => $learner['identity']]);
                }
            }
            catch (Exception\InvalidFormException $e) {
                // do nothing, form validation
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot change password. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'message' => $message,
            'form'    => $form,
            'learner' => $learner
        ];
    }

    /**
     * Forgot password request
     * Todo: !! add a check that the 'current' learner has an Email before processing this function any further !!
     */
    public function forgotPasswordAction ()
    {
        $request = $this->getRequest();
        $message = false;
        // form
        $form = $this->form('Learner\Form\ForgotPassword');

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                $identity = $data['identity'];

                /* @var $service \Learner\Service\LearnerService */
                $service = $this->service('Learner\Service');

                // process forgot password
                $learner = $service->forgotPassword($identity);

                // success!
                $message['success'] = $this->translate("Forgot password request was successful. Please check your email for the reset password link to change your password.");
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
                if ($e->getMessage() == 'Cannot reset password for the learner. Learner does not exist in the system') {
                    $message['error'] = $this->translate('Cannot reset password for the learner. Learner does not exist in the system.');

                } elseif ($e->getMessage() == 'Cannot reset password for the learner. Learner does not have an email address') {
                    $message['error'] = $this->translate('Unable to initiate a password reset for this Learner. Please use the contact-us form to submit a password-reset request or contact your site administrator.');

                } else {
                    $message['error'] = $this->translate('Cannot process password change request. An internal error has occurred. Please try again later.');
                }
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
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
     * Delete learner
     */
    public function deleteAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $request = $this->getRequest();
        $message = false;
        $learner = $service->findOneByUserId($userId);

        // process request
        if ($this->params('confirm') === 'yes') {
            $service->delete($learner);

            // success
            $message['success'] = sprintf('%1$s was successfully deleted.', $learner['name']);
            if (!$request->isXmlHttpRequest()) {
                $this->flashMessenger()->addSuccessMessage($message['success']);
                return $this->redirect()->toRoute('learner/directory');
            }
        }

        return [
            'message' => $message,
            'learner' => $learner
        ];
    }

    /**
     * Activate learner
     */
    public function activateAction ()
    {
        try {
	        $userId = $this->params('user_id');

	        /* @var $service \Learner\Service\LearnerService */
	        $service = $this->service('Learner\Service');
	        $learner = $service->findOneByUserId($userId);

	        // process request
	        if ($this->params('confirm') === 'yes') {

	            // activate learner
	            $learner = $service->activate($userId);

	            // success
	            $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('%s was successfully activated.'), $learner['name']));
	            return $this->redirect()->toRoute('learner/directory');
	        }
        }
        catch (\Exception $e) {
			// failed
            $code = $e->getCode();
             switch ($code) {
            	case LearnerEvent::ERROR_CODE_MAX_REACHED:
            	    $this->flashMessenger()->addErrorMessage(sprintf($this->translate('The number of learners to activate exceeds the available space for learners in the company licence. %s'), $e->getMessage()));
            	    break;
            }
            return $this->redirect()->toRoute('learner/directory');
        }

        return [
            'learner' => $learner
        ];
    }

    /**
     * Deactivate learner
     */
    public function deactivateAction ()
    {
        $userId = $this->params('user_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
        $learner = $service->findOneByUserId($userId);

        // process request
        if ($this->params('confirm') === 'yes') {

            // deactivate learner
            $service->deactivate($userId);

            // success
            $this->flashMessenger()->addSuccessMessage(sprintf('%1$s was successfully deactivated.', $learner['name']));
            return $this->redirect()->toRoute('learner/directory');
        }

        return [
            'learner' => $learner
        ];
    }

    /**
     * Import / bulk upload
     */
    public function importAction ()
    {
        $siteId = $this->params('site_id');

        /* @var $service \Learner\Service\LearnerService */
        $service = $this->service('Learner\Service');
    //    $site = $service->createSiteEntity($siteId);

        /* @var $form \Learner\Form\ImportForm */
        $form = $this->form('Learner\Form\Import');
        $request = $this->getRequest();
        $message = false;
  //      $csvUpload = $this->getRequest()->getCookie()->csvUpload;
  //      if ($csvUpload == 'success') {
  //          $message['success'] = $this->translate("CSV data was successfully uploaded.");
  //      }

        // process form submit
        if ($post = $this->post(false)) {
			try {
			    // validate form
			    $data = $form->validate($post);

				// step 1. upload file to the destination folder
			    /* @var $options \Learner\Service\Options */
				$options = $this->service('Learner\Options');

                /* @var $licenseService \Licence\Company\Service\LicenceService */
                $licenseService = $this->service('Licence\Company\Service');
                $numLearners    = $licenseService->findLearnerLimitBySiteId ($siteId);

                // check if we have previously set an error
                $licenceFailed = 0;
                if (isset($this->getRequest()->getCookie()->licenceFailed)) {
                    $licenceFailed = $this->getRequest()->getCookie()->licenceFailed;
                }

                // get the number of active learners for the site
                $learners = $this->service('Learner\Active');

                if (isset($numLearners['numlearners']) && count($learners) >= $numLearners['numlearners'] && $licenceFailed != count($learners)) {
                    // license limit will be exceeded - return error
                    $this->flashMessenger()->addInfoMessage($this->translate('The company licence has reached the maximum number of learners that can be registered. Please contact an administrator.'));
                    $cookie = new SetCookie('licenceFailed', count($learners), time() + 30, '/');
                    $this->getResponse()->getHeaders()->addHeader($cookie);
                    return $this->redirect()->refresh();
                }

				// filename
				$fileUploadPath = $options->getUploadPath() . DIRECTORY_SEPARATOR . 'bulk';
				$sourceFilename = $data['file_upload']['name'];
				$destinationFilename = $fileUploadPath . DIRECTORY_SEPARATOR . pathinfo($sourceFilename, PATHINFO_FILENAME) . '-' . date("YmdHis") . '.' . pathinfo($sourceFilename, PATHINFO_EXTENSION);

				// upload file
				/* @var $fileManager \Savve\FileManager\FileManager */
				$fileManager = $this->service('Savve\FileManager');
				$fileManager->upload($sourceFilename, $destinationFilename, $data);

				// read the CSV file
				/* @var $csvService \Savve\Csv\Csv */
				$csvService = $this->service('Savve\Csv');
				$data = $csvService->read($destinationFilename);

				// save data
				$service->bulkUpload($data, $siteId, $numLearners['numLearners'], count($learners));

			    // success!
			//    $cookie = new SetCookie('csvUpload', 'success', time() + 30, '/');
			//    $this->getResponse()->getHeaders()->addHeader($cookie);
			    $message['success'] = $this->translate('CSV data was successfully uploaded.');
			    if (!$request->isXmlHttpRequest()) {
			        $this->flashMessenger()->addSuccessMessage($message['success']);
			        return $this->redirect()->refresh();
			    }
            }
            catch (Exception\InvalidFormException $e)  {
                // do nothing, form validation exception
            }
            catch (\Exception $e) {
                // failed!
                if ($e->getMessage() == 'Empty CSV Uploaded') {
                    $message['error'] = $this->translate('Cannot import the CSV file data. No data was found within the CSV file, or, the data was incomplete or invalid.');
                    if (!$request->isXmlHttpRequest()) {
    			        $this->flashMessenger()->addSuccessMessage($message['error']);
    			        return $this->redirect()->refresh();
    			    }
                } else {
                    if (strpos($e->getMessage(), 'could not be renamed. It already exists') !== false) {
                        $message['error'] = $this->translate('CSV upload error. Please rename the file and try again.');
                        if (!$request->isXmlHttpRequest()) {
                            $this->flashMessenger()->addSuccessMessage($message['error']);
                            return $this->redirect()->refresh();
                        }
                    } elseif (strpos($e->getMessage(), 'Unable to update/add learner(s)') !== false) {
                        $message['error'] = $e->getMessage();
                        if (!$request->isXmlHttpRequest()) {
                            $this->flashMessenger()->addErrorMessage($message['error']);
                            return $this->redirect()->refresh();
                        }

                    } elseif (strpos($e->getMessage(), 'Company Learner Licence Exceeded') !== false) {
                        $message['error'] = $this->translate($e->getMessage() . ' Please contact your support administrator or try again later.');

                        if (!$request->isXmlHttpRequest()) {
                            $this->flashMessenger()->addErrorMessage($message['error']);
                            return $this->redirect()->refresh();
                        }

                    } else {
                        $message['error'] = $this->translate('Cannot import CSV file. An internal error has occurred: ' . $e->getMessage() . ' Please contact your support administrator or try again later.');
                        if (!$request->isXmlHttpRequest()) {
                            $this->flashMessenger()->addSuccessMessage($message['error']);
                            return $this->redirect()->refresh();
                        }
                    }
                }
            }
        }

        return [
            'message' => $message,
            'form' => $form
        ];
    }

    /**
     * Download learner directory as a CSV
     */
    public function csvAction ()
    {
        try {
            $siteId = $this->params('site_id');
            /* @var $service \Learner\Service\LearnerService */
            $service = $this->service('Learner\Service');
            $learners = $service->fetchAllBySiteId($siteId);

            /* @var $options \Learner\Service\Options */
            $options = $this->service('Learner\Options');
            $filePath = $options->getUploadPath() . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR;
            $filename = $filePath . 'learners-' . date('Ymd') . '.csv';
            Stdlib\FileUtils::makeDirectory($filePath);

            $columns = [
                'learner_first_name' => 'first_name',
                'learner_last_name' => 'last_name',
                'learner_gender' => 'gender',
                'learner_email' => 'email',
                'learner_telephone' => 'telephone',
                'learner_mobile_number' => 'mobile_number',
                'learner_street_address' => 'street_address',
                'learner_suburb' => 'suburb',
                'learner_postcode' => 'postcode',
                'learner_state' => 'state',
                'learner_country' => 'country',
                'learner_password' => 'password',
                'learner_cpd_id' => 'cpd_id',
                'learner_cpd_number' => 'cpd_number',
                'learner_referrer' => 'referrer',
                'learner_note' => 'note',
                'learner_subscription' => 'subscription',
                'learner_status' => 'status',
                'agent_code' => 'agent_code',
                'employment_id' => 'employment_id',
                'employment_location' => 'location',
                'employment_cost_centre' => 'cost_centre',
                'employment_position' => 'position',
                'employment_type' => 'employment_type',
                'employment_manager' => 'manager',
                'employment_start_date' => 'start_date',
                'employment_end_date' => 'end_date'
            ];

            foreach ($learners as $key => $learner) {
                $learners[$key]['learner_password'] = '';
            }

            // create the CSV file
            $model = new \Savve\Csv\View\Model\CsvModel();
            $model->setVariable('columns', $columns);
            $model->setVariable('learners', $learners);
            $model->setOption('outputFilename', $filename);

            return $model;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}