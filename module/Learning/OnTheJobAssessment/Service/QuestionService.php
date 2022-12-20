<?php

namespace OnTheJobAssessment\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Doctrine\Common\Collections\ArrayCollection;

class QuestionService extends AbstractService
{

    /**
     * Find ONE learning activity by activity ID
     *
     * @param integer $activityId
     * @return Entity\LearningActivity
     */
    public function findOneLearningActivityById ($activityId)
    {
        $repository = $this->learningRepository();

        // create query
        $qb = $repository->createQueryBuilder('activity')
            ->leftJoin('activity.site', 'site')
            ->leftJoin('activity.assessment', 'assessment')
            ->leftJoin('assessment.questions', 'questions')
            ->select('activity, site, assessment, questions')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);
        //	->add('orderBy', 'questions.sortOrder ASC');

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ONE question by question ID
     *
     * @param integer $questionId
     * @return Entity\AssessmentQuestions
     */
    public function findOneQuestionById ($questionId)
    {
        $repository = $this->questionsRepository();

        // create query
        $qb = $repository->createQueryBuilder('question')
            ->select('question')
            ->where('question.questionId = :questionId')
            ->setParameter('questionId', $questionId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ALL questions by activity ID
     *
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function findAllQuestionsByActivityId ($activityId)
    {
        $repository = $this->questionsRepository();

        // create query
        $qb = $repository->createQueryBuilder('question')
            ->leftJoin('question.assessment', 'assessment')
            ->leftJoin('assessment.activity', 'activity')
            ->select('question, assessment, activity')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Create the assessment questions
     *
     * @param array $data
     * @return Entity\AssessmentActivity
     * @throws \Exception
     */
    public function createQuestions ($data)
    {
        try {
	        $data = Stdlib\ObjectUtils::extract($data);
	        $entityManager = $this->getEntityManager();

	        // define the activity ID
	        $routeMatch = $this->routeMatch();
	        $activityId = isset($data['activity_id']) ? $data['activity_id'] : $routeMatch->getParam('activity_id');

	        // get the learning activity instance
	        $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);
	        $data['activity'] = $activity;

	        // create the entity
	        $assessment = new Entity\AssessmentActivity();
	        $assessment = Stdlib\ObjectUtils::hydrate($data, $assessment);

	        // save in repository
	        $entityManager->persist($assessment);

	        // associate the new questions to the assessment
	        foreach ($assessment['questions'] as $question) {
	            $question['assessment'] = $assessment;
	            $entityManager->persist($question);
	        }
	        $entityManager->flush();
	        $entityManager->clear();

	        return $assessment;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update the assessment questions
     *
     * @param Entity\AssessmentActivity $data
     * @return Entity\AssessmentActivity
     * @throws \Exception
     */
    public function updateQuestions ($data)
    {
        try {
            // abject object
            $data = Stdlib\ObjectUtils::extract($data);

            // titty manager
            $entityManager = $this->getEntityManager();

            // get the assessment entity
            $assessmentId = $data['assessment_id'];
            $assessment = $entityManager->getReference('Savvecentral\Entity\AssessmentActivity', $assessmentId);
            $assessment = Stdlib\ObjectUtils::hydrate($data, $assessment);

            // save the questions
            $questions  = $data['questions'];

            // set this to 'NULL' to prevent error reporting from Doctrine whilst trying to save the 'questions' entity part of the data
            $assessment['questions'] = null;

            // save in repository
            $entityManager->persist($assessment);
            $entityManager->flush($assessment);

            // local container for updated/new questions
            $arr = array();

            // update existing or add new questions to the assessment
            foreach ($questions as $question) {
                $questionId = $question['question_id'];
                if (isset($questionId) && strlen($questionId)) {
                    $quest = $entityManager->getReference('Savvecentral\Entity\AssessmentQuestions', $questionId);
                    $quest = Stdlib\ObjectUtils::hydrate($question, $quest);
                } else {
                    $quest = new Entity\AssessmentQuestions();
                    $quest = Stdlib\ObjectUtils::hydrate($question, $quest);
                }
                $quest['assessment'] = $assessment;
                $arr[] = $quest;
                $entityManager->persist($quest);
                // save inside each loop - doesn't seem to save correctly outside the loop
                $entityManager->flush($quest);
            }

            // place the updated quesdtion back into the response
            $assessment['questions'] = $arr;

            $entityManager->persist($assessment);
            $entityManager->flush($assessment);

            // clear the entities
            $entityManager->clear();

            // reload
            $activity = $assessment['activity'];

            sleep(5);
            return $this->findOneLearningActivityById($activity['activityId']);// reloadAssessmentByActivityId($activity['activityId']);

        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Find ALL questions by activity ID
     *
     * @param integer $activityId
     * @return ArrayCollection
     */
    public function reloadAssessmentByActivityId ($activityId)
    {
        $repository = $this->activityRepository();

        // create query
        $qb = $repository->createQueryBuilder('assessment')
            ->leftJoin('assessment.questions', 'questions')
            ->leftJoin('assessment.activity', 'activity')
            ->select('assessment, questions, activity')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Delete ONE assessment question
     *
     * @param integer $questionId
     * @throws \Exception
     */
    public function deleteQuestion ($questionId)
    {
        try {
	        $repository = $this->questionsRepository();
	        $question = $repository->findOneByQuestionId($questionId);

	        $entityManager = $this->getEntityManager();
	        $entityManager->remove($question);
	        $entityManager->flush($question);
	        $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the notification doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learningRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');
        return $repository;
    }

    /**
     * Get the AssessmentActivity doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function activityRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AssessmentActivity');
        return $repository;
    }

    /**
     * Get the AssessmentQuestions doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function questionsRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\AssessmentQuestions');
        return $repository;
    }
}