<?php

namespace Elucidat\Controller;

use Doctrine\ORM\EntityManager;
use Savve\Stdlib\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class ManageController extends AbstractActionController
{

    /**
     * Doctrine Entity Manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Add a elucidat account on Savvecentral
     */
    public function addAccountAction()
    {
        $form = $this->form('Elucidat\Form\Create');
        if($post = $this->post(false)){
            try {
                // validate form
                $data = $form->validate($post);
                /** @var \Elucidat\Elucidat\Elucidat $service */
                $service = $this->getServiceLocator()->get('Elucidat\Elucidat');

                $messages = [];


                $messages[] = $this->translate("Elucidat account created in Savvecentral.");


                if($data['elucidat_customer_code'] == null){
                    //create a new elucidat Account
                    $response= $service->create($data);

                    if(isset($response['customer_code'])){
                        $messages[] = $this->translate("Matching account created in Elucidat.");
                        $data['elucidat_customer_code'] = $response['customer_code'];
                    }
                    else if($response['errors']){
                        // failed
                        $this->flashMessenger()->addErrorMessage($this->translate('Error occured trying to create account in Elucidat. Please check details and try again.'));
                        return $this->redirect()->refresh();
                    }
                }

                //create a account in savvecentral
                try{
                    $elucidatAccount = $service->createElucidatAccount($data);
                }
                catch(\Exception $e){
                    $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. The email addresses need to be unique. Please contact your support administrator or try again later.')));
                    return $this->redirect()->refresh();
                }

                $this->flashMessenger()->addInfoMessage(implode("",$messages));
                return $this->redirect()->toRoute('elucidat/directory');

            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. An internal error has occurred. Please contact your support administrator or try again later.')));

                return $this->redirect()->refresh();
            }
        }
        return [
            'form' => $form
        ];
    }

    /**
     * Add a elucidat account on Savvecentral
     */
    public function linkAccountAction()
    {
        $form = $this->form('Elucidat\Form\Link');
        //get the customer code from route
        $customerCode = $this->getParam('customer_code');
        //retrieve the paid accounts from the response returned
        $allAccounts = $this->getServiceLocator()->get('Elucidat\AllElucidatLicences');

        //Find the paid account matching this account
        $matchingAccount = null;
        if(count($allAccounts) > 0){
            $matchingAccount = array_filter($allAccounts,function($allAccount) use ($customerCode){
                return $customerCode  == $allAccount['customer_code'];
            });
        }

        if(!$matchingAccount || count($matchingAccount) == 0){
            throw new \Exception("Unable to locate account matching the specified parameters. Please try again later");
        }
        $matchingAccount = count($matchingAccount) > 0 ? array_shift(array_values($matchingAccount)) : null;

        if($post = $this->post(false)){
            try {
                // validate form
                $data = $form->validate($post);

                /** @var \Elucidat\Elucidat\Elucidat $service */
                $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
                $messages = [];

                $data['company_name'] = $matchingAccount['company_name'];
                $data['company_email'] = $matchingAccount['company_email'];
                $data['first_name'] = $matchingAccount['first_name'];
                $data['last_name'] = $matchingAccount['last_name'];
                $data['postcode'] = $matchingAccount['postcode'];
                $data['country'] = $matchingAccount['country'];
                $data['elucidat_customer_code'] = $matchingAccount['customer_code'];
                $messages[] = $this->translate("Elucidat account created in Savvecentral.");

                //create a account in savvecentral
                try{
                    $elucidatAccount = $service->createElucidatAccount($data);
                }
                catch(\Exception $e){
                    $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. The email addresses need to be unique. Please contact your support administrator or try again later.')));
                    return $this->redirect()->refresh();
                }

                $this->flashMessenger()->addInfoMessage(implode("",$messages));
                return $this->redirect()->toRoute('elucidat/directory');

            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. An internal error has occurred. Please contact your support administrator or try again later.')));

                return $this->redirect()->refresh();
            }
        }
        return [
            'form' => $form,
            'account'=>$matchingAccount
        ];
    }

    /**
     *  Unlink / remove an Elucidat account from savvecentral
     */
    public function unlinkAccountAction()
    {
    	//get the customer code from route
    	$customerCode = $this->getParam('customer_code');

    	$form = $this->form('Elucidat\Form\Unlink');

    	//get the customer code from route
    	$customerCode = $this->getParam('customer_code');
    	//retrieve the paid accounts from the response returned
    	$allAccounts = $this->getServiceLocator()->get('Elucidat\AllElucidatLicences');

    	//Find the paid account matching this account
    	$matchingAccount = null;
    	if(count($allAccounts) > 0){
    		$matchingAccount = array_filter($allAccounts,function($allAccount) use ($customerCode){
    			return $customerCode  == $allAccount['customer_code'];
    		});
    	}

    	if(!$matchingAccount || count($matchingAccount) == 0){
    		throw new \Exception("Unable to locate account matching the specified parameters. Please try again later");
    	}
    	$matchingAccount = count($matchingAccount) > 0 ? array_shift(array_values($matchingAccount)) : null;

    	/** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
    	$site 	 = $service->getSiteByCustomerCode($customerCode);
    	$site = $site['site'];
    	$site['unlink_id'] 				= $site['siteId'];
    	$site['elucidat_customer_code'] = $customerCode;

		$form->bind($site);

    	if($post = $this->post(false)){
    		try {
    			// validate form
    			$data = $form->validate($post);
    			//unlink the account in savvecentral
    			try{
    				$elucidatAccount = $service->unlinkElucidatAccount($data);
    				$this->flashMessenger()->addSuccessMessage(sprintf($this->translate('The elucidat account ' . $data['elucidat_customer_code'] . ' was successfully unlinked.')));
    				return $this->redirect()->toRoute('elucidat/directory');
    			}
    			catch(\Exception $e){
    				$this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot Un-Link or Delete the elucidat account. Please contact your support administrator or try again later.')));
    				return $this->redirect()->refresh();
    			}
    		//	$this->flashMessenger()->addInfoMessage(implode("",$messages));
    		//	return $this->redirect()->toRoute('elucidat/directory');

    		}
    		catch (Exception\InvalidFormException $e) {
    			// form validation error, do nothing
    		}
    		catch (\Exception $e) {
    			// failed
    			$this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. An internal error has occurred. Please contact your support administrator or try again later.')));
    			return $this->redirect()->refresh();
    		}
    	}
    	return [
    	'site' => $site,
    	'form' => $form,
    	'account'=>$matchingAccount
    	];
    }

    /**
     * Add a elucidat account on Savvecentral
     */
    public function updateAccountAction()
    {
        $form = $this->form('Elucidat\Form\Update');
        $accountId = $this->params('account_id');

        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $account = $service->findOneAccountByAccountId($accountId);
        $account['site_id'] = $account['site']['site_id'];
        $form->bind($account);

        if($post = $this->post(false)){
            try {
                // validate form
                $data = $form->validate($post);
                $messages = [];
                $messages[] = $this->translate("Elucidat account updated in Savvecentral.");

                //create a new elucidat Account
                $response= $service->update($data);
                if(isset($response['status']) && $response['status'] == 200){
                    $messages[] = $this->translate("Matching account updated in Elucidat");
                }
                else if($response['errors']){
                    // failed
                    $this->flashMessenger()->addErrorMessage($this->translate('Error occured trying to create account in Elucidat. Please check details and try again.'));
                    return $this->redirect()->refresh();
                }

                //create a account in savvecentral
                try{
                    $account = $service->updateAccount($account);
                }
                catch(\Exception $e){
                    $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. The email addresses need to be unique. Please contact your support administrator or try again later.')));
                    return $this->redirect()->refresh();
                }

                $this->flashMessenger()->addInfoMessage(implode("",$messages));
                return $this->redirect()->refresh();

            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create elucidat account. An internal error has occurred. Please contact your support administrator or try again later.')));

                return $this->redirect()->refresh();
            }
        }
        return [
            'form' => $form
        ];
    }

    /**
     * Activate an elucidat account on savvecentral
     */
    public function activateAccountAction(){
    	$accountId = $this->params('account_id');
    	/** @var \Elucidat\Elucidat\Elucidat $service */
    	$service = $this->getServiceLocator()->get('Elucidat\Elucidat');
    	$account = $service->findOneAccountByAccountId($accountId);

    	if(!$account){
    		throw new \DomainException ("Unable to locate account. Internal error occured");
    	}

    	// process form request
    	if ($this->params('confirm') === 'yes') {
    		try {
    			$service->activateAccount($account);

    			$this->flashMessenger()->addInfoMessage($this->translate('Activated the elucidat account on Savv-e Central successfully.'),'info');
    			return $this->redirect()->toRoute('elucidat/directory');

    		} catch (\Exception $e) {
    			$this->flashMessenger ()
    			->addErrorMessage (sprintf ($this->translate ('Cannot activate account. An internal error has occurred. Please contact your support administrator or try again later.')));
    			$this->redirect ()->toRoute ('elucidat/directory');
    		}
    	}

    	return [
    	'account' => $account
    	];
    }

    /**
     * De-activate an elucidat account on savvecentral
     */
    public function deactivateAccountAction(){
    	$accountId = $this->params('account_id');
    	/** @var \Elucidat\Elucidat\Elucidat $service */
    	$service = $this->getServiceLocator()->get('Elucidat\Elucidat');
    	$account = $service->findOneAccountByAccountId($accountId);

    	if(!$account){
    		throw new \DomainException ("Unable to locate account. Internal error occured");
    	}

    	// process form request
    	if ($this->params('confirm') === 'yes') {
    		try {
    			$service->deactivateAccount($account);

    			$this->flashMessenger()->addInfoMessage($this->translate('Deactivated the elucidat account on Savv-e Central successfully.'),'info');
    			return $this->redirect()->toRoute('elucidat/directory');

    		} catch (\Exception $e) {
    			$this->flashMessenger ()
    			->addErrorMessage (sprintf ($this->translate ('Cannot deactivate account. An internal error has occurred. Please contact your support administrator or try again later.')));
    			$this->redirect ()->toRoute ('elucidat/directory');
    		}
    	}
    	return [
    		'account' => $account
    	];
    }

    public function createPublicKeysAction(){
       $accountId = $this->params('account_id');
       /** @var \Elucidat\Elucidat\Elucidat $service */
       $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
       $account = $service->findOneAccountByAccountId($accountId);
        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                // remove from repository
                $response = $service->createPublicKeysFor($account);
                if(isset($response['public_key']) && $response['public_key']){
                    $account['elucidat_public_key'] = $response['public_key'];
                    $service->updateAccount($account);
                }

                // success
                $this->flashMessenger()->addSuccessMessage($this->translate('Created public keys for the account successfully.'));
                return $this->redirect()->toRoute('elucidat/directory');
            }
            catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create public keys for the account "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $account['company_email']));
                $this->redirect()->toRoute('elucidat/directory');
            }
        }

        return [
            'account' => $account
        ];

    }


    /**
     * Display a list of all the tools that an admin can use .
     */
    public  function adminAction()
    {

    }

    /**
     * List all the elucidat accounts registered on Savvecentral
     */
    public function directoryAction()
    {
        try {
            /** @var \Elucidat\Elucidat\Elucidat $service */
            $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
            $accounts = $service->findAllAccounts();
            //retrieve the paid accounts from the response returned
            $allAccounts = $this->getServiceLocator()->get('Elucidat\AllElucidatLicences');

            //Find the paid account matching this account
            if(count($accounts) > 0){
                foreach($accounts as $account){

                    $matchingAccount = array_filter($allAccounts,function($allAccount) use ($account){
                        return $account['elucidat_customer_code'] == $allAccount['customer_code'];
                    });

                    $account['elucidat_account'] = count($matchingAccount) > 0 ? array_shift(array_values($matchingAccount)) : null;
                }
            }

            //get reseller web hooks
            $events=  $service->retrieveResellerWebhooks();

            return [
                'accounts'=>$accounts,
                'events' => $events
            ];
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [];
    }
    /**
     * Display ALL
     */
    public function paidDirectoryAction ()
    {

        try {
            //retrieve the paid accounts from the response returned
            $paidAccounts = $this->getServiceLocator()->get('Elucidat\paidElucidatLicences');

            $savvecentralAccounts = $this->getServiceLocator()->get('Elucidat\SavvecentralElucidatLicencesFactory');

            //Find the paid account matching this account
            if(count($paidAccounts) > 0){
                foreach($paidAccounts as &$paidAccount){
                    $matchingAccount = $savvecentralAccounts->filter(function($account) use ($paidAccount){
                        return $account['elucidat_customer_code'] == $paidAccount['customer_code'];
                    });
                    $paidAccount['association'] = null;
                    if(count($matchingAccount) > 0){
                        $paidAccount['association'] = $matchingAccount->first();
                    }
                }
            }

            return [
                'paidAccounts'=>$paidAccounts
            ];
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [];
    }
    /**
     * Display ALL
     */
    public function trialDirectoryAction ()
    {

        try {
            //retrieve the paid accounts from the response returned
            $trialAccounts = $this->getServiceLocator()->get('Elucidat\TrialElucidatLicences');

            $savvecentralAccounts = $this->getServiceLocator()->get('Elucidat\SavvecentralElucidatLicencesFactory');

            //Find the paid account matching this account
            if(count($trialAccounts) > 0){
                foreach($trialAccounts as &$trialAccount){
                    $matchingAccount = $savvecentralAccounts->filter(function($account) use ($trialAccount){
                        return $account['elucidat_customer_code'] == $trialAccount['customer_code'];
                    });
                    $trialAccount['association'] = null;
                    if(count($matchingAccount) > 0){
                        $trialAccount['association'] = $matchingAccount->first();
                    }
                }


            }

            return [
                'trialAccounts'=>$trialAccounts
            ];
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [];
    }
    /**
     * Display ALL
     */
    public function endedTrialDirectoryAction ()
    {

        try {
            /** @var \Elucidat\Elucidat\Elucidat $service */
            $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
            $clients = $service->retrieve();

            $trials = $clients['trial-ended'];

            return [
                'clients' => $clients,
                'trials'=>$trials
            ];
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [];
    }
}