<?php

namespace Group\Learner\Factory\Service;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MembersServiceFactory
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
        if (!$groupId) {
            throw new Exception\InvalidArgumentException(sprintf('Cannot load the group learners service. Missing paramter group_id.'), null, null);
        }

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
                AND learner.status NOT IN ('deleted', 'inactive')
                ORDER BY learner.firstName ASC, learner.lastName ASC";
        $params['groupId'] = $groupId;

        // execute query
        $results = $repository->fetchArray($dql, $params);

        return $results;
    }
}