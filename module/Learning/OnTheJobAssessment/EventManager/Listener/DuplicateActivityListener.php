<?php

namespace OnTheJobAssessment\EventManager\Listener;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;

class DuplicateActivityListener
{

    /**
     * Invoke the event listener
     *
     * @param EventInterface $event
     */
    public function __invoke (EventInterface $event)
    {
        /* @var $service \Learning\Service\LearningService */
        $service = $event->getTarget();

        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $service->getServiceLocator();

        /* @var $entityManager \Savve\Doctrine\ORM\EntityManager */
        $entityManager = $service->getEntityManager();

        // get the original learning activity
        $original = $event->getParam('original');

        // get the duplicate learning activity
        $duplicate = $event->getParam('duplicate');

        // only duplicate WRITTEN-ASSESSMENT learning activity
        if (!($original['activity_type'] === $duplicate['activity_type'] && $original['activity_type'] === 'on-the-job-assessment')) {
            return;
        }

        // duplicate the assessment activity data
        $assessment = $original['assessment'];
        if ($assessment) {
            $data = Stdlib\ObjectUtils::extract($assessment);

            // create new assessment activity data
            $newAssessment = new Entity\AssessmentActivity();
            $newAssessment = Stdlib\ObjectUtils::hydrate($data, $newAssessment);
            $newAssessment['activity'] = $duplicate;

            // persist assessment
            $entityManager->persist($newAssessment);
            $entityManager->flush($newAssessment);

            // duplicate the questions, if any
            $questions = $assessment['questions'];
            if ($questions || count($questions)) {
                foreach ($questions as $question) {
                    $data = Stdlib\ObjectUtils::extract($question);

                    // create new question as duplicate of original
                    $newQuestion = new Entity\AssessmentQuestions();
                    $newQuestion = Stdlib\ObjectUtils::hydrate($data, $newQuestion);
                    $newQuestion['assessment'] = $newAssessment;

                    // persist question
                    $entityManager->persist($newQuestion);
                }
            }
            $entityManager->flush();
            $entityManager->clear();
        }
    }
}