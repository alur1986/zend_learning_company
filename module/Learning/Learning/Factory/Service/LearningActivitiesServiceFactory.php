<?php

namespace Learning\Factory\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class LearningActivitiesServiceFactory implements
        FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        /* @var $service \Learning\Service\LearningService */
        $service = $serviceLocator->get('Learning\Service');

        $entityManager = $service->getEntityManager();
        $params = [];
        $dql[] = "SELECT
				activity.activityId AS value,
                activity.title AS label,
                activity.activityId AS activity_id,
                activity.title AS title,
                activity.description AS description,
                activity.activityType AS activity_type,
                activity.status AS status

                ,(SELECT COUNT(l1.userId)
                	FROM Savvecentral\Entity\Learner l1
                	LEFT JOIN l1.distribution d1
                	LEFT JOIN d1.activity a1
                	WHERE a1.activityId = activity.activityId AND d1.status NOT IN ('deleted','disapproved','enrolled')
                	AND l1.status IN ('new', 'active')) AS num_learners

                ,(SELECT COUNT(e2.eventId)
                	FROM Savvecentral\Entity\Event e2
                	LEFT JOIN e2.activity a2
                	WHERE a2.activityId = activity.activityId AND e2.status IN ('enabled')) AS num_events

                FROM Savvecentral\Entity\LearningActivity activity
                LEFT JOIN activity.site site
                LEFT JOIN activity.distribution distribution
                LEFT JOIN distribution.learner learner
                WHERE activity.status NOT IN ('deleted')";

        $routeMatch = $serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch && ($siteId = $routeMatch->getParam('site_id')))) {
            return false;
        }

        $dql[] = "AND site.siteId = :siteId";
        $params['siteId'] = $siteId;

        $dql[] = "GROUP BY activity.activityId";
        $dql[] = "ORDER BY activity.title ASC";

        $dql = implode(' ', $dql);
        $query = $entityManager->createQuery($dql)
            ->setParameters($params);
        $results = $query->getArrayResult();

        return $results ?  : [];
    }
}