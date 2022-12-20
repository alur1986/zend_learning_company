<?php

namespace Group\Learner\Factory\Service;

use Doctrine\Common\Collections\Collection;
use Zend\ServiceManager\ServiceLocatorInterface;

class GroupAdminsServiceFactory
{

    /**
     * Get all the learner admins given a group ID
     *
     * @param ServiceLocatorInterface $serviceManager
     * @return Collection
     */
    public function __invoke (ServiceLocatorInterface $serviceManager)
    {
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $repository = $entityManager->getRepository('Savvecentral\Entity\GroupLearners');

        /* @var $routeMatch \Zend\Mvc\Router\Http\RouteMatch */
        $routeMatch = $serviceManager->get('Application')
            ->getMvcEvent()
            ->getRouteMatch();
        if (!($routeMatch instanceof \Zend\Mvc\Router\Http\RouteMatch)) {
            return [];
        }
        $routeName = $routeMatch->getMatchedRouteName();
        $groupId = $routeMatch->getParam('group_id');

        // create query
        $dql = "SELECT
                learner.userId as learner_id,
                learner.firstName AS first_name,
                learner.lastName AS last_name,
                CONCAT(learner.firstName,' ', learner.lastName) AS name,
                learner.email AS email,
                learner.telephone AS telephone,
                learner.mobileNumber AS mobile_number,
                learner.status AS status,

                groupLearner.role AS role,
                groups.groupId AS group_id,
                groups.name AS group_name

                FROM Savvecentral\Entity\GroupLearners groupLearner
                LEFT JOIN groupLearner.learner learner
                LEFT JOIN groupLearner.group groups
                WHERE groups.groupId = :groupId
                AND groupLearner.role = :role
                ORDER BY learner.firstName ASC, learner.lastName ASC";
        $params['groupId'] = $groupId;
        $params['role'] = 'admin';

        // execute query
        $results = $repository->fetchArray($dql, $params);

        return $results;
    }
}