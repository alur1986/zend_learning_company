<?php

namespace Elucidat\Controller;

use Doctrine\ORM\EntityManager;
use Savve\Stdlib\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Savvecentral\Entity\ElucidatUser;
use Zend\Mvc\Controller\AbstractActionController;

class AuthorController extends AbstractActionController
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
     * Link a elucidat User to a savvecentral user
     */
    public function createAction(){
        $form = $this->form('Elucidat\Form\CreateUser');
        $accountId = $this->params('account_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $account = $service->findOneAccountByAccountId($accountId);


        //set up the user element
        $element = $form -> get('user_id');
        /* @var $service \Learner\Service\LearnerService */
        $service = $this->serviceLocator->get('Learner\Service');
        $learners = $service->findAllActiveBySiteId($account['site']['site_id']);
        $valueOptions = [];
        foreach ($learners as $learner) {
            $array = [
                'user_id'             => $learner['user_id'], 'first_name' => $learner['first_name'],
                'last_name'           => $learner['last_name'], 'name' => $learner['name'], 'email' => $learner['email'],
                'telephone'           => isset($learner['telephone']) ? $learner['telephone'] : null,
                'mobile_number'       => isset($learner['mobile_number']) ? $learner['mobile_number'] : null,
                'address'             => isset($learner['address']) ? $learner['address'] : null,
                'status'              => $learner['status'],
                'profile_picture_uri' => isset($learner['profile_picture_uri']) ? $learner['profile_picture_uri'] : null,
                'profile_picture'     => isset($learner['profile_picture']) ? $learner['profile_picture'] : null,
                'role'                => isset($learner['role']) ? $learner['role'] : null,
                'employment_id'       => isset($learner['employment_id']) ? $learner['employment_id'] : null,
                'site'                => isset($learner['site']) ? $learner['site'] : null,
            ];

            $valueOptions[] = array_merge ([
                                               'label' => $learner['name'], 'value' => $learner['user_id']
                                           ], $array);
        }
        $element->setValueOptions ($valueOptions);


        if(!$account){
            throw new \Exception("Unable to locate account specified. Please contact an administrator");
        }

        if($post = $this->post(false)){
            try {
                // validate form
                $data = $form->validate($post);

                $user = $this->entityManager->getReference('Savvecentral\Entity\Learner',$post['user_id']);
                $data['first_name'] = $user['first_name'];
                $data['last_name'] = $user['last_name'];
                $data['email'] = $post['elucidat_email'];

                // change service
                /** @var \Elucidat\Elucidat\Elucidat $service */
                $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
                $response = $service->createAuthorForElucidatAccount($account,$data);

                $author = new ElucidatUser();
                $author->setAccount($account);
                $author->setUser($user);
                $author->setElucidatEmail($data['elucidat_email']);
                $author->setHasElucidatAccess($data['has_elucidat_access']);
                $service->updateAccountAuthor($author);

                $this->flashMessenger()->addInfoMessage($this->translate('The author for the elucidat account has been successfully created.'),'info');
                return $this->redirect()->toRoute('elucidat/manage-authors',['account_id'=>$account['id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {
                // failed
                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot create authors. An internal error has occurred. Please contact your support administrator or try again later.')));

                return $this->redirect()->refresh();
            }

        }

        return [
            'form' => $form,
            'account'=>$account
        ];
    }

    /**
     * Link a elucidat User to a savvecentral user
     */
    public function linkAction(){

        $email = $this->params('email');
        $accountId = $this->params('account_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $account = $service->findOneAccountByAccountId($accountId);

        //get the authors
        $response = $service->retrieveAuthors($account);
        if($response['status']!==200 ){
            throw new \Exception("Retrieving authors failed.");
        }

        $authors = $response['response'];
        if(count($authors) == 0){
            throw new \Exception("Unable to locate Authors.");
        }

        $matchingAuthor = array_filter($authors,function($author) use($email){
            return $author['email'] == $email;
        });
        $matchingAuthor = count($matchingAuthor) > 0 ? array_shift(array_values($matchingAuthor)) : null;

        $form = $this->form('Elucidat\Form\LinkUser');

        $element = $form -> get('user_id');
        /* @var $service \Learner\Service\LearnerService */
        $learnerService = $this->serviceLocator->get('Learner\Service');
        $learners = $learnerService->findAllActiveBySiteId($account['site']['site_id']);
        $valueOptions = [];
        foreach ($learners as $learner) {
            $array = [
                'user_id'             => $learner['user_id'], 'first_name' => $learner['first_name'],
                'last_name'           => $learner['last_name'], 'name' => $learner['name'], 'email' => $learner['email'],
                'telephone'           => isset($learner['telephone']) ? $learner['telephone'] : null,
                'mobile_number'       => isset($learner['mobile_number']) ? $learner['mobile_number'] : null,
                'address'             => isset($learner['address']) ? $learner['address'] : null,
                'status'              => $learner['status'],
                'profile_picture_uri' => isset($learner['profile_picture_uri']) ? $learner['profile_picture_uri'] : null,
                'profile_picture'     => isset($learner['profile_picture']) ? $learner['profile_picture'] : null,
                'role'                => isset($learner['role']) ? $learner['role'] : null,
                'employment_id'       => isset($learner['employment_id']) ? $learner['employment_id'] : null,
                'site'                => isset($learner['site']) ? $learner['site'] : null,
            ];

            $valueOptions[] = array_merge ([
                                               'label' => $learner['name'], 'value' => $learner['user_id']
                                           ], $array);
        }

        $element->setValueOptions ($valueOptions);


        if($post = $this->post(false)){
            try {
                // validate form
                $data = $form->validate($post);

                $user = $this->entityManager->getReference('Savvecentral\Entity\Learner',$post['user_id']);

                $author = new ElucidatUser();
                $author->setAccount($account);
                $author->setUser($user);
                $author->setElucidatEmail($matchingAuthor['email']);
                $author->setHasElucidatAccess($data['has_elucidat_access']);
                $service->updateAccountAuthor($author);

                $this->flashMessenger()->addInfoMessage($this->translate('The author for the elucidat account has been successfully linked.'),'info');
                return $this->redirect()->toRoute('elucidat/manage-authors',['account_id'=>$account['id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation error, do nothing
            }
            catch (\Exception $e) {

                $this->flashMessenger()->addErrorMessage(sprintf($this->translate('Cannot link author. An internal error has occurred. Please contact your support administrator or try again later.')));
                return $this->redirect()->refresh();
            }

        }

        return [
            'form' => $form,
            'account'=>$account,
            'matchingAuthor' =>  $matchingAuthor
        ];
    }

    /**
     * Delete an elucidat Author
     */
    public function deleteAction(){
        $accountId = $this->params('account_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $account = $service->findOneAccountByAccountId($accountId);

        $authorId = $this->params('author_id');
        $author = $service->findOneAuthorByAuthorId($authorId);

        if(!$author){
            throw new \DomainException ("Unable to locate author. Internal error occured");
        }

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                //delete from elucidat
                $service->deleteAuthorForElucidatAccount($author);

                $service->deleteElucidatUser($author);

                $this->flashMessenger()->addInfoMessage($this->translate('Successfully deleted author for elucidat account.'),'info');
                return $this->redirect()->toRoute('elucidat/manage-authors',['account_id'=>$account['id']]);

            } catch (\Exception $e) {
                $this->flashMessenger ()
                     ->addErrorMessage (sprintf ($this->translate ('Cannot delete author for this account. An internal error has occurred. Please contact your support administrator or try again later.')));
                $this->redirect ()->toRoute ('elucidat/manage-authors', array('account_id' => $account['id']));
            }
        }
        return [
            'account' => $account,
            'author' => $author
        ];
    }
    /**
     * Activate  an elucidat Author and allow him/her to access elucidat through savvecentral
     */
    public function activateAction(){
        $authorId = $this->params('author_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $author = $service->findOneAuthorByAuthorId($authorId);

        if(!$author){
            throw new \DomainException ("Unable to locate author. Internal error occured");
        }

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                $service->activateAuthor($author);

                $this->flashMessenger()->addInfoMessage(sprintf($this->translate('Successfully allowed author to access elucidat from Savv-e Central.')),'info');
                return $this->redirect()->toRoute('elucidat/manage-authors',['account_id'=>$this->params('account_id')]);

            } catch (\Exception $e) {
                $this->flashMessenger ()
                     ->addErrorMessage (sprintf ($this->translate ('Cannot activate author. An internal error has occurred. Please contact your support administrator or try again later.')));
                $this->redirect ()->toRoute ('elucidat/manage-authors', array('account_id'=>$this->params('account_id')));
            }
        }
        return [
            'author' => $author
        ];
    }

    /**
     * De-activate  an elucidat Author and allow him/her to access elucidat through savvecentral
     */
    public function deactivateAction(){
        $authorId = $this->params('author_id');
        /** @var \Elucidat\Elucidat\Elucidat $service */
        $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
        $author = $service->findOneAuthorByAuthorId($authorId);

        if(!$author){
            throw new \DomainException ("Unable to locate author. Internal error occured");
        }

        // process form request
        if ($this->params('confirm') === 'yes') {
            try {
                $service->deactivateAuthor($author);

                $this->flashMessenger()->addInfoMessage(sprintf($this->translate('Successfully disabled author access to elucidat from Savv-e Central.')),'info');
                return $this->redirect()->toRoute('elucidat/manage-authors',['account_id'=>$this->params('account_id')]);

            } catch (\Exception $e) {
                $this->flashMessenger ()
                     ->addErrorMessage (sprintf ($this->translate ('Cannot deactivate author. An internal error has occurred. Please contact your support administrator or try again later.')));
                $this->redirect ()->toRoute ('elucidat/manage-authors', array('account_id'=>$this->params('account_id')));
            }
        }
        return [
            'author' => $author
        ];
    }

    /**
     * List all the authors
     */
    public function directoryAction(){

        try {
            $accountId = $this->params('account_id');
            /** @var \Elucidat\Elucidat\Elucidat $service */
            $service = $this->getServiceLocator()->get('Elucidat\Elucidat');
            $account = $service->findOneAccountByAccountId($accountId);

            if(!$account){
                throw new \Exception("Unable to locate account for this id.");
            }

            //get the authors
            $response = $service->retrieveAuthors($account);
            if($response['status']!==200){
                throw new \Exception("Retrieving authors failed.");
            }

            $authors = $response['response'];
            $currentAuthors = $service->findAllAuthorsByAccountId($accountId);
            //Find the paid account matching this account
            if(count($authors) > 0 && count($currentAuthors)>0){
                foreach($authors as &$author){
                    $matchingAccount = $currentAuthors->filter(function($user) use ($author){
                        return $user->elucidatEmail == $author['email'];
                    });

                    $author['association'] = null;
                    if(count($matchingAccount) > 0){
                        $author['association'] = $matchingAccount->first();
                    }
                }
            }
            return [
                'authors'=>$authors,
                'account'=>$account
            ];
        }
        catch (\Exception $e) {
            throw $e;
        }

        return [];
    }
}