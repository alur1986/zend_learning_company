<?php

namespace Authentication\Controller;

use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class AuthenticationController extends AbstractActionController
{
    /**
     * Learner login
     */
    public function loginAction ()
    {
        /* load any services we'll require */
        $service = $this->learnerService();

        /* $var $activityService \Savvecentral\Entity\LearningActivity*/
        $activityService = $this->service('Learning\Service');

        /* $var $distributionService \Savvecentral\Entity\DistributionLearning*/
        $distributionService = $this->service('Distribution\Learning');

        // site ID
        $siteId = $this->params('site_id');

        // get the login form
        $form = $this->form('Authentication\Form\Login');

        // if identity is set in the route/URL
        if ($identity = $this->params('identity')) {
            // use that as the username of the login form
            $form->get('identity')
                ->setValue($identity);
        }

        $message = false;
        $newLearner = isset($this->getRequest()->getCookie()->newLearner) ? $this->getRequest()->getCookie()->newLearner : false;
        if ($newLearner) {
            $message['success'] = $this->translate("You have successfully registered. Please login using your email, mobile number or employment ID, and the password that you just created.");
        }
        $newPassword = isset($this->getRequest()->getCookie()->newPassword) ? $this->getRequest()->getCookie()->newPassword : false;
        if ($newPassword) {
            $message['success'] = $this->translate("Your password has been successfully changed.");
        }

        // get and set the 'Site' helper
        $Site = $this->getViewHelper( 'Site' );
        $site = $Site();
        $rememberMe = false;
        if (isset($this->getRequest()->getCookie()->rememberMe)) {
            $rememberMe = $this->getRequest()->getCookie()->rememberMe;
        }
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $prefix = 'https://';
        } else {
            $prefix = 'http://';
        }
        if (isset($site['remember_me']) && $rememberMe && $site['remember_me'] == true && !isset($_POST['process-login-form']) && empty($_POST['process-login-form'])) {
            // process any Auto Distributions before redirecting
            $activities = $activityService->getAutoDistributeOnLogin($siteId);
            if (count($activities)) {
                $learner = $service->findOneByAuthenticationToken($rememberMe);
                $distributionService->autoDistributeActivities($activities, $learner, 'login');
            }
            $route = $prefix . $_SERVER['SERVER_NAME'] . '/learner/autologin/'.$rememberMe;
            return $this->redirect()->toUrl($route);
        }

        // process form submit
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // login
                $learner = $service->login($data['identity'], $data['password']);

                // check for any 'Auto Distribute' on Login activities
                $activities = $activityService->getAutoDistributeOnLogin($siteId);
                if (count($activities)) {
                    // distribute these activities to the new learner
                    $distributionService->autoDistributeActivities($activities, $learner, 'login');
                }

                /* @var $authorizationService \Authorization\Factory\Service\AuthorizationServiceFactory */
                $authorizationService = $this->service('Zend\Authorization\AuthorizationService');
                $role = $authorizationService->findOneRoleByLearnerId($learner['userId']);
                $roleId = ((isset($role['id'])) ? $role['id'] : null);
                // we currently only allow the 'learner' role (100001) to use the 'Remember Me' functionality due to the non-existent security policy
                if (isset($data['remember_me']) && $data['remember_me'] == true && ($roleId == 100001 || $role == 100002 || $roleId == null )) {
                    $key = $learner['authentication_token'];
                    if ($key) {
                        $cookie = new SetCookie('rememberMe', $key, time() + 60*60*24*30, '/');
                        $this->getResponse()->getHeaders()->addHeader($cookie);
                        $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Welcome back %s! Your login will be remembered for 1 month'), $learner['name']));
                    } else {
                        $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Welcome back %s!'), $learner['name']));
                    }
                } else {
                    // success
                    $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Welcome back %s!'), $learner['name']));
                }

                // redirect to URL if set
                $redirectUrl = isset($data['redirect_url']) && $data['redirect_url'] ? $data['redirect_url'] : (isset($post['redirect_url']) && $post['redirect_url'] ? $post['redirect_url'] : null);
                if ($redirectUrl) {
                    return $this->redirect()->toUrl($redirectUrl);
                }
                $route = $prefix . $_SERVER['SERVER_NAME'] . '/dashboard';
                return $this->redirect()->toUrl($route);
            //    return $this->redirect()->refresh();
            }
            catch (Exception\InvalidFormException $e) {
                // form validation, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate($e->getMessage())));
                return $this->redirect()->refresh();
            }
        }

        return [
            'message' => $message,
            'form'    => $form
        ];
    }

    /**
     * Learner logout
     */
    public function logoutAction ()
    {
        try {
            /* @var $authenticationService \Zend\Authentication\AuthenticationService */
            $authenticationService = $this->service('Zend\Authentication\AuthenticationService');

            // logout
            $service = $this->learnerService();
            $service->logout();

            // success
            $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('You have successfully logged out!')));
        }
        catch (\Exception $e) {
            // failed
        }
		return $this->redirect()->toRoute('home');
    }

    /**
     * Learner autologin/impersonate
     */
    public function impersonateAction ()
    {
        try {
            $authenticationToken = $this->params('authentication_token');
            $service = $this->learnerService();

            // site ID
            $siteId = $this->params('site_id');

            /* $var $activityService \Savvecentral\Entity\LearningActivity*/
            $activityService = $this->service('Learning\Service');

            /* $var $distributionService \Savvecentral\Entity\DistributionLearning*/
            $distributionService = $this->service('Distribution\Learning');

            // impersonate
            $learner = $service->impersonate($authenticationToken);

            // check for any 'Auto Distribute' on Login activities
            $activities = $activityService->getAutoDistributeOnLogin($siteId);
            if (count($activities)) {
                // distribute these activities to the new learner
                $distributionService->autoDistributeActivities($activities, $learner, 'login');
            }

            // success
            $newLearner = false;
            if (isset($this->getRequest()->getCookie()->newLearner)) {
                $newLearner = $this->getRequest()->getCookie()->newLearner;
            }
            if ($newLearner) {
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('%s, you have successfully registered and have been automatically directed to your Savve dashboard!'), $learner['name']));
                $cookie = new SetCookie('newLearner', false, time() - 120, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
            } else {
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Welcome back %s!'), $learner['name']));
            }
            return $this->redirect()->toRoute('login');
        }
        catch (\Exception $e) {
            // failed
            $this->flashMessenger()->addErrorMessage(sprintf($this->translate($e->getMessage())));
            if (isset($this->getRequest()->getCookie()->rememberMe)) {
                $cookie = new SetCookie('rememberMe', false, time() - 100, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
            }
            return $this->redirect()->toRoute('login');
        }
    }

    /**
     * Learner sso login
     */
    public function ssoAction()
    {
        try {
            $token = $this->params('jwt_token');
            $siteId = $this->params('site_id');
            $learnerService = $this->learnerService();
            $learner = $learnerService->ssoLogin($token, $siteId);

            /* $var $activityService \Savvecentral\Entity\LearningActivity*/
            $activityService = $this->service('Learning\Service');

            /* $var $distributionService \Savvecentral\Entity\DistributionLearning*/
            $distributionService = $this->service('Distribution\Learning');

            // check for any 'Auto Distribute' on Login activities
            $activities = $activityService->getAutoDistributeOnLogin($siteId);
            if (count($activities)) {
                // distribute these activities to the new learner
                $distributionService->autoDistributeActivities($activities, $learner, 'login');
            }
            $newLearner = false;
            if (isset($this->getRequest()->getCookie()->newLearner)) {
                $newLearner = $this->getRequest()->getCookie()->newLearner;
            }
            if ($newLearner) {
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('%s, you have successfully registered and have been automatically directed to your Savve dashboard!'), $learner['name']));
                $cookie = new SetCookie('newLearner', false, time() - 120, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
            } else {
                $this->flashMessenger()->addSuccessMessage(sprintf($this->translate('Welcome back %s!'), $learner['name']));
            }
            // redirect to URL if set
            $redirectUrl = $this->getRequest()->getQuery('redirect_url');
            if (!empty($redirectUrl)) {
                return $this->redirect()->toUrl($redirectUrl);
            }
            return $this->redirect()->toRoute('dashboard');
        }
        catch(\Exception $e) {
            // failed
            $this->flashMessenger()->addErrorMessage(sprintf($this->translate($e->getMessage())));
            if (isset($this->getRequest()->getCookie()->rememberMe)) {
                $cookie = new SetCookie('rememberMe', false, time() - 100, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
            }
            return $this->redirect()->toRoute('login');
        }
    }

    /**
     * Get the learner doctrine service
     * @return \Learner\Service\LearnerService
     */
    public function learnerService ()
    {
        return $this->getServiceLocator()->get('Learner\Service');
    }
}
