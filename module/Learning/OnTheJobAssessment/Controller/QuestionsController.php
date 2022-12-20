<?php

namespace OnTheJobAssessment\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class QuestionsController extends AbstractActionController
{

    /**
     * Preview ONE on-the-job-assessment with questions
     */
    public function previewAction ()
    {
        $activityId = $this->params('activity_id');
        $service = $this->questionService();
        $activity = $service->findOneLearningActivityById($activityId);
        $assessment = $activity['assessment'];
        $questions = $assessment['questions'];

        $message = false;
        // if a success meesage is passed
        $directoryMessageSuccess = $this->getRequest()->getCookie()->directoryMessageSuccess;
        if ($directoryMessageSuccess) {
            $message['success'] = $this->translate($directoryMessageSuccess);
        }
        // if an errore is passed
        $directoryMessageError = $this->getRequest()->getCookie()->directoryMessageError;
        if ($directoryMessageError) {
            $message['error'] = $this->translate($directoryMessageError);
        }

        return [
            'activity'   => $activity,
            'assessment' => $assessment,
            'questions'  => $questions,
            'message'    => $message
        ];
    }

    /**
     * Display ALL on-the-job-assessment questions
     */
    public function directoryAction ()
    {
        $activityId = $this->params('activity_id');
        $service = $this->questionService();
        $activity = $service->findOneLearningActivityById($activityId);

        return [
            'activity' => $activity
        ];
    }

    /**
     * Create on-the-job-assessment questions
     */
    public function createAction ()
    {
        $activityId = $this->params('activity_id');
        $activityService = $this->learningActivityService();
        $activity = $activityService->findOneOTJById($activityId);
        $assessment = $activity['assessment'];
        $request = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('OnTheJobAssessment\Form\CreateQuestion');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // save in repository
                $service = $this->questionService();

                // if new, then create
                if (!$assessment) {
                    $assessment = $service->createQuestions($data);
                }
                else {
                    $assessment = $service->updateQuestions($data);
                }
                $activity = $assessment['activity'];

                $form->get('activity_id')->setValue($activityId);

            //    $form->populateValues(Stdlib\ObjectUtils::extract($assessment), true);
                $form->bind($assessment);

                // success
                $message['success'] = $this->translate('Created the questions for the learning activity successfully.');
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
                $message['error'] = $this->translate('Cannot save the questions for the on-the-job-assessment. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        } else {
            $form->get('activity_id')->setValue($activityId);
            if ($assessment) {
                $form->bind($assessment);
            }
        }

        return [
            'activity' => $activity,
            'form'     => $form,
            'message'  => $message
        ];
    }

    /**
     * Display ONE on-the-job-assessment question
     */
    public function readAction ()
    {
        $questionId = $this->params('question_id');
        $service = $this->questionService();
        $question = $service->findOneQuestionById($questionId);
        $activity = $question['assessment']['activity'];

        return [
            'activity' => $activity
        ];
    }

    /**
     * Delete ONE on-the-job-assessment question
     */
    public function deleteAction ()
    {
        $questionId = $this->params('question_id');
        $service = $this->questionService();
        $question = $service->findOneQuestionById($questionId);
        $activity = $question['assessment']['activity'];
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') === 'yes') {
            try {
                // delete the venue
                $service->deleteQuestion($questionId);

                // success
                $message = $this->translate('The learning activity assessment question has been deleted successfully.');
         //       $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
         //       $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/on-the-job-assessment/preview', ['activity_id' => $activity['activity_id']]);
            }
            catch (\Exception $e) {
                // failed
                $message = $this->translate('Cannot delete the learning activity assessment question. An internal error has occurred. Please contact your support administrator or try again later.');
        //        $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
        //        $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/on-the-job-assessment/preview', ['activity_id' => $activity['activity_id']]);
            }
        }

        return [
        	'question' => $question,
            'activity' => $activity
        ];
    }

    /**
     * Get the OnTheJobAssessment service provider
     *
     * @return \OnTheJobAssessment\Service\OnTheJobAssessmentService
     */
    protected function learningActivityService ()
    {
        return $this->service('OnTheJobAssessment\Service');
    }

    /**
     * Get the On-The-Job-Assessment Question service provider
     *
     * @return \OnTheJobAssessment\Service\QuestionService
     */
    protected function questionService ()
    {
        return $this->service('OnTheJobAssessment\QuestionService');
    }
}