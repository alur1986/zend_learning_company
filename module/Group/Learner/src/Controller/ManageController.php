<?php

namespace Group\Learner\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;

class ManageController extends AbstractActionController
{
    /**
     * Display ALL learners within ONE group
     */
    public function directoryAction ()
    {
        $groupId   = $this->params('group_id');
        $service   = $this->groupLearnerService();
        $request   = $this->getRequest();
        $message   = false;
        $name 	   = $service->getGroupNameByGroupId($groupId);

        // get form
        $form = $this->form('Group\Learner\Form\LearnersUpdate');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // remove learners from the group
                if (isset($post['remove'])) {
                    $learnerIds = (array) $data['learner_id'];
                    $groupIds = (array) $groupId;

                    // remove learners from the repository
                    $service->removeLearnersFromGroups($learnerIds, $groupIds);

                    // success
                    $message['success'] = $this->translate('The learners have been removed from the group successfully.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['success']);
                    }
                }

                // set selected learners as admin
                if (isset($post['admin'])) {
                    $learnerIds = (array) $data['learner_id'];
                    $groupIds = (array) $groupId;

                    // set learners as admin in repository
                    $service->setLearnersAsAdminInGroups($learnerIds, $groupIds);

                    // success
                    $message['success'] = $this->translate('The group has been updated successfully.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['success']);
                    }
                }

                // set selected learners as learners
                if (isset($post['learner'])) {
                    $learnerIds = (array) $data['learner_id'];
                    $groupIds = (array) $groupId;

                    // set learners as learner in repository
                    $service->setLearnersAsLearnerInGroups($learnerIds, $groupIds);

                    // success
                    $message['success'] = $this->translate('The group has been updated successfully.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['success']);
                    }
                }
                if (!$request->isXmlHttpRequest()) {
                    // success if HTTP request
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('The group could not be updated. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    $this->redirect() ->refresh();
                }
            }
        }

        // get all the learners within the group
        $group 		   = $service->findAllLearnersCurrentlyInGroup($groupId);// $service->findOneGroupById($groupId);
    //    $groupLearners = $this->service('Group\Learner\All');
        $valueOptions  = [];

        foreach ($group as $key=>$learner) {
            $groupLearner =  $learner['groupLearners'];
            $group[$key]['role'] = $groupLearner[0]['role'];
            $option = [
                'label' => $learner['name'],
                'value' => $learner['learner_id']
            ];
            if (is_array($learner)) {
                $valueOptions[] = array_merge($option, $learner);
            }
        }

        // set form data
        if ($form->get('learner_id')) {
            $form->get('learner_id')->setValueOptions($valueOptions);
        }

        return [
            'message' => $message,
            'group'   => $group,
            'name'    => $name,
            'form' 	  => $form
        ];
    }

    /**
     * Add NEW learners to ONE group
     */
    public function learnersAction ()
    {
        $groupId = $this->params('group_id');
        $siteId  = $this->params('site_id');
        $service = $this->groupLearnerService();
        $request = $this->getRequest();
        $message = false;

        // get form
        $form = $this->form('Group\Learner\Form\LearnersAdd');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);
                // add learners to the group learners repository
                $learnerIds = $data['learner_id'];
                if(is_null($groupId)){
                    throw new \Exception("Unable to locate group");
                }
                else if(!is_array($groupId)){
                    $groupId = [$groupId];
                }

                $service->addLearnersToGroups((array) $learnerIds, (array) $groupId);

                // success
                $message['success'] = $this->translate('Learners were added to the group successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot add selected learners to the group. An internal error has occurred. Please try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['error']);
                    $this->redirect()->refresh();
                }
            }
        }

        // get learners list of learners 'not' already in group
        $group = $service->getLearnersNotInGroup($groupId, $siteId);

        return [
            'message' => $message,
            'group'   => $group,
            'form'    => $form
        ];
    }

    /**
     * Add NEW learner to ONE group BY CSV import
     */
    public function importAction ()
    {
        $groupId = $this->params('group_id');
        $service = $this->groupLearnerService();
        $request  = $this->getRequest();

        // form
        $form = $this->form('Group\Learner\Form\Import');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // if file was uploaded
                if (isset($data['file_upload']) && ($data['file_upload']['size'] !== 0 || $data['file_upload']['error'] !== 4)) {

                    /* @var $option \Group\Learner\Service\Option */
                    $option = $this->service('Group\Learner\Options');
                    $fileUploadPath = $option->getUploadPath();

                    $sourceFileName = $data['file_upload']['name'];
                    $destinationFileName = $fileUploadPath . DIRECTORY_SEPARATOR . pathinfo($sourceFileName, PATHINFO_FILENAME) . '.' . pathinfo($sourceFileName, PATHINFO_EXTENSION);

                    // upload file
                    /* @var $fileManager \Savve\FileManager\FileManager */
                    $fileManager = $this->service('Savve\FileManager');
                    $fileManager->upload($sourceFileName, $destinationFileName, $data);

                    // read the CSV file
                    /* @var $csvService \Savve\Csv\Csv */
                    $csvService = $this->service('Savve\Csv');
                    $csvData = $csvService->read($destinationFileName);

                    // import from CSV data
                    $groupIds = (array) $groupId;
                    $service->importLearnersFromCsv($csvData, $groupIds);
                }

                // success
                $message['success'] = $this->translate('The learners have been added to the group successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation, do nothing
            }
            catch (\Exception $e) {
                // failed
                if ($e->getMessage() == 'Empty CSV Uploaded') {
                    // empty CSV
                    $message['error'] = $this->translate('Cannot import the CSV file data. No data was found within the CSV file, or, the data was incomplete or invalid.');
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['error']);
                    }
                } elseif (strpos($e->getMessage(), "Unable to update/add the following learner")) {
                    // learners not found in DB
                    $message['error'] = sprintf($this->translate('%s'), $e->getMessage());
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['error']);
                    }
                } else {
                    // other error
                    $message['error'] = sprintf($this->translate('%s'), $e->getMessage());
                    if (!$request->isXmlHttpRequest()) {
                        $this->flashMessenger()->addSuccessMessage($message['error']);
                    }
                }
                if (!$request->isXmlHttpRequest()) {
                    return $this->redirect()->refresh();
                }
            }
        }
        $group = $service->findOneGroupById($groupId);

        return [
            'message' => $message,
            'group'   => $group,
            'form'    => $form
        ];
    }

    /**
     * Get the group learners service provider
     *
     * @return \Group\Learner\Service\GroupLearnerService
     */
    protected function groupLearnerService ()
    {
        return $this->service('Group\Learner\Service');
    }
}