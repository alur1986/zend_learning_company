<?php

namespace Report\AssessmentSummary\Service;

use Report\EventManager\Event;
use Report\Service\ReportService as AbstractService;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ReportService extends AbstractService
{

    /**
     * Find ALL events by site ID
     *
     * // taken from 'namespace Event\Service'
     *
     * @param integer $siteId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllEventsBySiteId ($siteId)
    {
        $repository = $this->eventRepository();

        // create query
        $qb = $repository->createQueryBuilder('event')
            ->leftJoin('event.activity', 'activity')
            ->leftJoin('activity.site', 'site')
            ->select('event, activity, site')
            ->where('site.siteId = :siteId')
            ->andWhere('activity.status NOT IN (:activityStatus)')
            ->andWhere('activity.activityType IN (:activityType)')
            ->setParameter('activityStatus',['deleted'])
            ->setParameter('activityType',['written-assessment','on-the-job-assessment'])
            ->andWhere('event.status NOT IN (:eventStatus)')
            ->setParameter('eventStatus',[''])
            ->setParameter('siteId', $siteId)
            ->add('orderBy', 'activity.activityId ASC, event.status ASC, activity.title ASC, event.startDate ASC, event.endDate DESC');

        // execute query
        $results = $repository->fetchCollection($qb);

        return $results;
    }

    /**
     * Get activity type/reference
     *
     * @param integer $activityId
     * @return string
     */
    public function getActivityType ($activityId)
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');

        $qb = $repository->createQueryBuilder('activity')
            ->select('activity.activityType')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);
        // execute query
        $result = $repository->fetchCollection($qb);
        return $result[0];
    }

    /**
     * Get activity name/reference
     *
     * @param integer $activityId
     * @return string
     */
    public function getActivityName ($activityId)
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');

        $qb = $repository->createQueryBuilder('activity')
            ->select('activity.title')
            ->where('activity.activityId = :activityId')
            ->setParameter('activityId', $activityId);
        // execute query
        $result = $repository->fetchCollection($qb);
        return $result[0];
    }

    /**
     * Get event name/reference
     *
     * @param integer $eventId
     * @return string
     */
    public function getEventName ($eventId)
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Event');

        $qb = $repository->createQueryBuilder('event')
            ->select('event.title')
            ->where('event.eventId = :eventId')
            ->setParameter('eventId', $eventId);
        // execute query
        $result = $repository->fetchCollection($qb);
        return $result[0];
    }

    /**
     * Find ALL Learners that have completed supplied 'event'
     *
     * @param integer $eventId
     * @return ArrayCollection
     */
    public function findAllLearnersByEventId ($eventId)
    {
        $entityManager = $this->getEntityManager();

        // learner status
        $status = [ 'new', 'active', 'inactive' ];

        $dql[] = "SELECT
            learner.userId      AS learner_id,
            learner.firstName   AS learner_first_name,
            learner.lastName    AS learner_last_name,
            CONCAT( learner.firstName,' ', learner.lastName ) AS learner_name,
            learner.email       AS learner_email,
            ta.completionStatus AS evaluated,
        	te.completionStatus AS evaluation

            FROM Savvecentral\Entity\EventDistribution de
            LEFT JOIN de.learner learner
            LEFT JOIN de.trackerEvents te
            LEFT JOIN de.trackerAssessment ta

            WHERE de.event = :eventid
        	AND de.learner = learner.userId
            AND (te.completionStatus = 'completed' OR te.completionStatus = 'passed' OR te.completionStatus = 'failed' OR ta.completionStatus = 'completed' OR ta.completionStatus = 'evaluated')

            GROUP BY learner.userId";

        $params['eventid'] = $eventId;

        // order by
        $dql[] = "ORDER BY learner.firstName, learner.lastName";

         // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, 60 * 60 * 15, Stdlib\StringUtils::dashed($dql))
            ->getScalarResult();

        return $results;
    }

    /**
     * Create the report
     *
     * @param integer $learnerId
     * @param integer $eventId
     * @param integer $activityId
     * @return array $report Array containing the data of the report
     */
    public function report ($learnerId, $eventId, $activityId)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $results = [];
        $params = [];

        // create query
        $dql = "SELECT
                	learner.firstName AS learner_first_name,
                	learner.lastName AS learner_last_name,
                	CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                	learner.status AS learner_status,
                	site.siteId AS site_id,
                	site.name AS site_name,
                	te.score AS event_score,
                    te.lastAccessed AS last_access,
                    event.created AS event_created,
        		    event.startDate AS start_date,
        		    event.endDate AS end_date,
                	ta.completionStatus AS assessment_status,
                    ta.learnerComments  AS learner_comment,
                    ta.assessorComments AS assessor_comment,
        			ti.completedOn AS completed_date,
        		    te.completionStatus AS assessment_state

                FROM Savvecentral\Entity\EventDistribution ed
                LEFT JOIN ed.event event
                LEFT JOIN ed.learner learner
                LEFT JOIN learner.site site
        		LEFT JOIN learner.distribution di
                LEFT JOIN ed.trackerEvents te
                LEFT JOIN ed.trackerAssessment ta
        		LEFT JOIN di.trackerActivity ti

                WHERE ed.learner = :learnerId AND ed.event = :eventId AND di.activity = :activityId";

        $params['learnerId']    = $learnerId;
        $params['eventId']      = $eventId;
        $params['activityId']   = $activityId;

        // execute query
        $results = $entityManager->createQuery($dql)
        	->setParameters($params)
        	->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        return $results;
    }

    /**
     * Load the Questions and Answers for their given Activity/Event/Learner
     *
     * @param integer $learnerId
     * @param integer $eventId
     * @param integer $activityId
     * @return array  $assessment Array containing the questions and answers
     */
    public function getAssessmentData ($learnerId, $eventId, $activityId)
    {

        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $results = [];
        $params = [];

        // create query
        $dql = "SELECT
                    question.question AS assessment_question,
                    tracker.learnerComments AS learner_answer,
                    tracker.assessorComments AS assessor_comment

                FROM Savvecentral\Entity\Event event
                LEFT JOIN event.eventDistribution distribution
                LEFT JOIN distribution.trackerQuestions tracker
                LEFT JOIN tracker.question question

                WHERE event.eventId = :eventId AND event.activity = :activityId AND distribution.learner = :learnerId";

        $params['learnerId']    = $learnerId;
        $params['eventId']      = $eventId;
        $params['activityId']   = $activityId;

        // execute query
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->getScalarResult();;

    //    $results =  $entityManager->fetchCollection($qb);

        return $results;

    }

    /**
     * Learning Activity doctrine entity repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function activityRepository ()
    {
    	$entityManager = $this->getEntityManager();
    	$repository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');
    	return $repository;
    }

    /**
     * Event doctrine entity repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function eventRepository ()
    {
    	$entityManager = $this->getEntityManager();
    	$repository = $entityManager->getRepository('Savvecentral\Entity\Event');
    	return $repository;
    }
}