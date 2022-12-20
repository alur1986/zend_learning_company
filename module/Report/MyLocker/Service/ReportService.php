<?php

namespace Report\MyLocker\Service;

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
     * Create the report
     *
     * @param array $filter Array of filters to use to generate the report
     * @return array $report Array containing the data of the report
     */
    public function report (array $filter)
    {
        /* @var @repository \Savve\Doctrine\Repository\AbstractRepository */
        $entityManager = $this->getEntityManager();

        $categoryIds = array_key_exists('category_id', $filter) ? (array) $filter['category_id'] : null;
        $learnerIds = array_key_exists('learner_id', $filter) ? (array) $filter['learner_id'] : null;
        $verificationStatus = array_key_exists('verification_status', $filter) ? $filter['verification_status'] : null;

        // if learner ID is not given
        if (!$learnerIds) {
            throw new Exception\InvalidArgumentException(sprintf('Learner ID was not given.'), null, null);
        }

        // create query
        $dql = [];
        $where = [];
        $params = [];
        $dql[] = "SELECT
                	partial locker.{resourceId},
                	locker.title AS resource_title,
                	locker.description AS resource_description,
                	locker.filename AS resource_filename,
                	locker.filetype AS resource_filetype,
                	locker.created AS resource_created,

                	lockerCategory.notes AS verification_notes,
                	lockerCategory.status AS verification_status,

                	category.taxonomyId AS category_id,
                	category.term AS category_term,
                	category.description AS category_description,
                	category.slug AS category_slug,
                	category.type AS category_type,
                	category.termGroup AS category_group,

					learner.userId AS learner_id,
                	learner.firstName AS learner_first_name,
                	learner.lastName AS learner_last_name,
                	CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                	learner.email AS learner_email,
                	learner.telephone AS learner_telephone,
                	learner.mobileNumber As learner_mobile_number,
                	learner.status AS learner_status,
                	employment.employmentId AS employment_id,
					employment.employmentType AS employment_type,
					employment.position AS employment_position,
					employment.startDate AS employment_start_date,
					employment.endDate AS employment_end_date,

                	site.siteId AS site_id,
                	site.name AS site_name

               	FROM Savvecentral\Entity\MyLocker locker
              	LEFT JOIN locker.learner learner
                LEFT JOIN locker.myLockerCategory lockerCategory
                LEFT JOIN lockerCategory.category category
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.groupLearners groupLearners
                LEFT JOIN groupLearners.group groups";

        // learner ID
        $where[] = "learner.userId IN(:learnerId)";
        $params['learnerId'] = $learnerIds;

        // category IDs
        if ($categoryIds) {
            $where[] = "category.taxonomyId IN (:categoryId) ";
            $params['categoryId'] = $categoryIds;
        }

        // verification status
        if ($verificationStatus) {
            $where[] = "lockerCategory.status IN (:verificationStatus)";
            $params['verificationStatus'] = $verificationStatus;
        }

        // merge all WHERE clauses
        if ($where) {
            $dql[] = sprintf("WHERE %s", implode(' AND ', $where));
        }

        // group by
        $dql[] = "GROUP BY locker.resourceId, category.taxonomyId";

        // order by
        $dql[] = "ORDER BY learner.firstName, learner.lastName, locker.title, lockerCategory.status";

        // execute query
        $dql = implode(' ', $dql);
        $results = $entityManager->createQuery($dql)
        	->setParameters($params)
        	->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        // trigger event listeners
        $eventManager = $this->getEventManager();
        $eventResults = $eventManager->trigger(new Event(Event::EVENT_REPORT_POST, $this, [ 'result' => $results ]), function  ($items) { return is_array($items) || $items instanceof \Traversable; });
        if ($eventResults->stopped()) {
            $results = $eventResults->last();
        }

        return $results;
    }
}