<?php

namespace LearningPlan\Service;

use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Doctrine\Service\AbstractService;
use Zend\Stdlib\AbstractOptions;

class LearningPlan extends AbstractService
{

    /**
     * Gets all Learning Plans for the given SiteID
     *
     * @param $siteId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllBySiteId ($siteId)
    {
        // retrieves all learning plans for a site
        $repository = $this->planRepository();

        $dql[] = "SELECT plans, activity, activities
                    FROM Savvecentral\Entity\LearningPlan plans
                    LEFT JOIN plans.hasActivities activity
                    LEFT JOIN activity.activities activities
                    WHERE plans.site = :siteId
                    AND plans.status NOT IN ('deleted')
                    ORDER BY plans.planId DESC";

        $params['siteId'] = $siteId;

        $plans = $repository->fetchCollection($dql, $params);

        return $plans;
    }

    /**
     * Gets one Learning Plan for the given PlanID
     *
     * @param $id
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findOneLearningPlanById($id)
    {
        // retrieve one learning plan
        $repository = $this->planRepository();

        $qb = $repository->createQueryBuilder('plans')
            ->select('plans')
            ->where('plans.planId = :planId')
            ->setParameter('planId', $id);

        $plan = $repository->fetchOne($qb);

        return $plan;
    }

    /**
     * Load one Learning Plan and its associated Activities
     * @param $id
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllByPlanId($id, $userId = false)
    {
        // retrieve one learning plan
        $repository = $this->planRepository();
        $dql[] = "SELECT plans, activity, activities
                    FROM Savvecentral\Entity\LearningPlan plans
                    LEFT JOIN plans.hasActivities activity
                    LEFT JOIN activity.activities activities
                    WHERE plans.planId = :planId
                    AND activities.status NOT IN ('deleted')
                    ORDER BY activities.ordering, activities.title ASC";
        $params['planId'] = $id;
        $plan = $repository->fetchCollection($dql, $params);

        if ($userId) {
            $activities = $plan[0]['hasActivities'];
            $arr = array();
            $entityManager = $this->getEntityManager();
            // process the 'prerequisite' details
            if(is_array($activities)) {
                foreach ($activities as $activity) {
                    $prerequisite = $activity['prerequisite'];
                    if (isset($prerequisite) && $prerequisite >= 1) {

                        // default response
                        $prerequisiteData['status'] = 'N/A';
                        $prerequisiteData['activity_id'] = $prerequisite;

                        $repository = $entityManager->getRepository('Savvecentral\Entity\LearningActivity');
                        $qb = $repository->createQueryBuilder('activity')
                            ->select('activity')
                            ->where('activity.activityId = :activityId')
                            ->setParameter('activityId', $prerequisite);

                        $result = $repository->fetchOne($qb);
                        if (isset($result['title'])) {
                            $prerequisiteData['title'] = $result['title'];
                        }

                        $activity['prerequisite'] = $prerequisiteData;
                    }
                    $arr[] = $activity;
                }
                $plan[0]['hasActivities'] = $arr;
            }
        }
        return $plan;
    }

    /**
     * Load all Learning Plans and associated Activities for a Learner
     * @param $learnerId
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllByLearnerId($learnerId)
    {
        // retrieve one learning plan
        $repository = $this->planRepository();
        $dateNow = new \DateTime();

        $dql[] = "SELECT plans, distribution, activity, activities
                    FROM Savvecentral\Entity\LearningPlan plans
                    LEFT JOIN plans.distribution distribution
                    LEFT JOIN distribution.learner learner
                    LEFT JOIN distribution.activity dactivity
                    LEFT JOIN plans.hasActivities activity
                    LEFT JOIN activity.activities activities
                    WHERE learner.userId = :learnerId AND distribution.distributionDate <= :dateNow
                    AND plans.status NOT IN ('deleted')
                    ORDER BY dactivity.ordering ASC";

        $params['learnerId'] = $learnerId;
        $params['dateNow']   = $dateNow;

        $plans = $repository->fetchCollection($dql, $params);

        return $plans;
    }

    /**
     * Load all Learning Plans and associated Activities by Plan ID
     * @param $plans
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllActivitiesByPlanId($plans)
    {
        // retrieve one learning plan
        $planIds = explode(",", $plans);

        $repository = $this->planRepository();
        $dql[] = "SELECT plans, distribution, activity, activities
                    FROM Savvecentral\Entity\LearningPlan plans
                    LEFT JOIN plans.distribution distribution
                    LEFT JOIN distribution.learner learner
                    LEFT JOIN plans.hasActivities activity
                    LEFT JOIN activity.activities activities
                    WHERE plans.planId IN (:planIds)
                    AND plans.status NOT IN ('deleted')";

        $params['planIds'] = $planIds;

        $plans = $repository->fetchCollection($dql, $params);

        return $plans;
    }

    /**
     * Creates a new Learning Plan
     *
     * @param $data
     * @return Stdlib\stdClass|Entity\LearningPlan
     * @throws \Exception
     */
    public function createPlan($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);
            $entityManager = $this->getEntityManager();

            // define the site ID
            $routeMatch = $this->routeMatch();
            $siteId = isset($data['site']) ? $data['site_id'] : $routeMatch->getParam('site_id');

            // get the site entity instance
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
            $data['site'] = $site;

            $data['created'] = new \DateTime();

            // create new entity
            $plan = new Entity\LearningPlan();
            $plan = Stdlib\ObjectUtils::hydrate($data, $plan);

            // save in repository
            $entityManager->persist($plan);
            $entityManager->flush($plan);

            return $plan;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Updates a Learning Plan
     *
     * @param $data
     * @return Stdlib\stdClass|Entity\LearningPlan
     * @throws \Exception
     */
    public function updatePlan($data)
    {
        try {
            // got a hot date
            $data = Stdlib\ObjectUtils::extract($data);

            // check if plan ID was provided
            if (!(isset($data['plan_id']) && $data['plan_id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot update learning plan. Plan ID was not provided'), 404, null);
            }
            $planId = $data['plan_id'];

            // entertainment manager
            $entityManager = $this->getEntityManager();

            // get the learning plan
            $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
            $plan = Stdlib\ObjectUtils::hydrate($data, $plan);

            // save in repository
            $entityManager->persist($plan);
            $entityManager->flush($plan);

            return $plan;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete ONE learning plan from repository
     *
     * @param $planId
     * @return mixed
     * @throws \Exception
     */
    public function deletePlan ($planId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // retrieve the learning plan
            $repository = $this->planRepository();
            $plan = $repository->findOneByPlanId($planId);

            // save in repository
            $plan['status'] = 'deleted';
            $entityManager->persist($plan);
            $entityManager->flush($plan);

            // ensure we delete and activity to plan relationships from hasPlans
            $qb = $entityManager->createQuery('Delete FROM Savvecentral\Entity\LearningPlanActivity hasPlans WHERE hasPlans.plam = :planId')
                ->setParameter('planId', $planId);
            $rows = $qb->execute();

            return $plan;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Activate ONE learning plan from repository
     *
     * @param $planId
     * @return mixed
     * @throws \Exception
     */
    public function activatePlan ($planId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // retrieve the learning activity
            $repository = $this->planRepository();
            $plan = $repository->findOneByPlanId($planId);

            // save in repository
            $plan['status'] = 'active';
            $entityManager->persist($plan);
            $entityManager->flush($plan);

            return $plan;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Deactivate ONE learning plan from repository
     *
     * @param integer $planId
     * @throws \Exception
     */
    public function deactivatePlan ($planId)
    {
        try {
            $entityManager = $this->getEntityManager();

            // retrieve the learning activity
            $repository = $this->planRepository();
            $plan = $repository->findOneByPlanId($planId);

            // save in repository
            $plan['status'] = 'inactive';
            $entityManager->persist($plan);
            $entityManager->flush($plan);

            return $plan;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function saveActivities ($data) {
        // got a hot date
        $data = Stdlib\ObjectUtils::extract($data);

        // check if plan ID was provided
        if (!(isset($data['plan_id']) && $data['plan_id'])) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot update learning plan. Plan ID was not provided'), 404, null);
        }
        $planId = $data['plan_id'];
        $config = $data['config'];
        $config = json_decode($config);
        $activityIds = array();

        foreach ($config as $key => $value) {
            $value = Stdlib\ObjectUtils::toArray($value);
            $activityIds[] = key($value);
        }

        // entertainment manager
        $entityManager = $this->getEntityManager();

        // before me 'clear the work matt' we  !! MUST !! save any existing references to ensure that we preserve any prerequisites that have been set
        $repository = $this->planRepository();
        $dql[] = "SELECT hasPlans
                FROM Savvecentral\Entity\LearningPlanActivity hasPlans
                WHERE hasPlans.plans IN (:planId)";

        $params['planId'] = $planId;
        $hasPlans = $repository->fetchCollection($dql, $params);

        // clear any existing references for this Learning Plan
        $entityManager->createQuery ('DELETE FROM Savvecentral\Entity\LearningPlanActivity learningPlanActivity WHERE learningPlanActivity.plans = :planId')
            ->setParameter ('planId', $planId)->execute ();

        // create an $index value that will be used to update the 'ordering' if the confirmation is set to TRUE
        $index = 0;
        $reorder = $data['confirm_ordering'];
        foreach ($activityIds as $activityId) {

            // create new learningPlanActivity entity
            $lpa = new Entity\LearningPlanActivity();
            $arr = array('plan_id' => $planId, 'activity_id' => $activityId);

            // get the plan entity instance
            $plan = $entityManager->getReference('Savvecentral\Entity\LearningPlan', $planId);
            $arr['plans'] = $plan;

            // get the plan entity instance
            $activity = $entityManager->getReference('Savvecentral\Entity\LearningActivity', $activityId);
            $arr['activities'] = $activity;

            // preserve the prerequisites if set
            foreach ($hasPlans as $hasPlan) {
                if ($hasPlan['plans']['plan_id'] == $planId && $hasPlan['activities']['activity_id'] == $activityId && $hasPlan['prerequisite'] != null) {
                //    $lpa->setPrerequisite($hasPlan['prerequisite']);
                    $arr['prerequisite'] = $hasPlan['prerequisite'];
                }
            }

            $lpa = Stdlib\ObjectUtils::hydrate($arr, $lpa);

            // save in repository
            $entityManager->persist($lpa);
            $entityManager->flush($lpa);

            if ($reorder == 1) {
                $activity['ordering'] = $index;
                // update activity
                $entityManager->persist($activity);
                $entityManager->flush($activity);
            }
            $index++;
        }
    }

    /**
     * Get the notification doctrine repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function planRepository ()
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\LearningPlan');
        return $repository;
    }

    /**
     * Get Learning Plan options
     *
     * @return AbstractOptions $options
     */
    public function getOptions ()
    {
        return $this->options;
    }

    /**
     * Set Learning Plan options
     *
     * @param \Zend\Stdlib\AbstractOptions $options
     */
    public function setOptions ($options)
    {
        $this->options = $options;
    }
}
