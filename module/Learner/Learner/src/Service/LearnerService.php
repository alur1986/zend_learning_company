<?php

namespace Learner\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;
use Learner\Event\Event;
use Learner\Exception as LearnerException;
use Savve\Doctrine\Service\AbstractService;
use Savve\Session\Container as SessionContainer;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savvecentral\Entity;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\Ldap as Ldap;
use Zend\Log\Filter\Priority as LogFilter;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriter;

class LearnerService extends AbstractService
{

    /**
     * Learner options
     *
     * @var Options
     */
    protected $options;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Options $options
     */
    public function __construct (EntityManager $entityManager, Options $options)
    {
        parent::__construct($entityManager);
        $this->options = $options;
    }

    /**
     * Find one learner by user_id
     *
     * @param integer $userId
     * @return Entity\Learner
     */
    public function findOneByUserId ($userId)
    {
        $repository = $this->learnerRepository();

        // create query // ,settings // LEFT JOIN learner.settings settings
        $dql = "SELECT learner, employment, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.site site
                WHERE learner.userId = :userId";

        $params['userId'] = $userId;

        // execute query
        $result = $repository->fetchOne($dql, $params);
        
        unset($result['settings']);
        unset($result['groupLearners']);
        unset($result['distribution']);
        unset($result['eventDistribution']);
        unset($result['notifications']);

        return $result;
    }

    /**
     * Find one learner by user_id
     *
     * @param integer $userId
     * @return Entity\Learner
     */
    public function findOneLearnerByUserId ($userId)
    {
        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner, employment')
            ->leftJoin('learner.employment', 'employment')
            ->where('learner.userId = :userId')
            ->setParameter('userId', $userId);

        // execute query
        $result = $repository->fetchOne($qb);

        //unset($result['site']);
        unset($result['settings']);
        unset($result['groupLearners']);
        unset($result['distribution']);
        unset($result['eventDistribution']);
        unset($result['notifications']);
        unset($result['userRoles']);
        unset($result['mylockers']);
        unset($result['elucidatUsers']);

        return $result;
    }

    /**
     * Find one learner by email
     *
     * @param string $email
     * @return Entity\Learner
     */
    public function findOneByEmail ($email)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT learner, employment, settings, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                LEFT JOIN learner.site site
                WHERE learner.email = :email";
        $params['email'] = $email;

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find one learner by mobile number
     *
     * @param string $mobileNumber
     * @return Entity\Learner
     */
    public function findOneByMobileNumber ($mobileNumber)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT learner, employment, settings, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                LEFT JOIN learner.site site
                WHERE learner.mobileNumber = :mobileNumber";
        $params['mobileNumber'] = $mobileNumber;

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find ONE learner by password token
     *
     * @param string $token
     * @return Entity\Learner
     */
    public function findOneByPasswordToken ($token)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT learner, employment, settings, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                LEFT JOIN learner.site site
                WHERE learner.passwordToken = :passwordToken";
        $params['passwordToken'] = $token;

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find ONE learner by authentication token
     *
     * @param string $token
     * @return Entity\Learner
     */
    public function findOneByAuthenticationToken ($token)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT learner, employment, settings, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                LEFT JOIN learner.site site
                WHERE learner.authenticationToken = :authenticationToken";
        $params['authenticationToken'] = $token;

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find ONE learner by employment ID
     *
     * @param string $employmentId
     * @return Entity\Learner
     */
    public function findOneByEmploymentId ($employmentId, $siteId = null)
    {
        $repository = $this->learnerRepository();

        // create query
        $dql = "SELECT learner, employment, settings, site
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                LEFT JOIN learner.site site
                WHERE employment.employmentId = :employmentId";
        $params['employmentId'] = $employmentId;

        // filter by site_id
        if ($siteId) {
            $dql .= "AND site.siteId = :siteId";
            $params['siteId'] = $siteId;
        }

        // execute query
        $result = $repository->fetchOne($dql, $params);

        return $result;
    }

    /**
     * Find ONE learner setting by user ID and settings name
     *
     * @param integer $userId
     * @param string $name
     * @return Entity\LearnerSettings
     */
    public function findOneSettingByUserId ($userId, $name)
    {
        $repository = $this->learnerSettingsRepository();

        // create query
        $qb = $repository->createQueryBuilder('settings')
            ->select('settings, learner')
            ->leftJoin('settings.learner', 'learner')
            ->where('settings.name = :name AND learner.userId = :userId')
            ->setParameter('name', $name)
            ->setParameter('userId', $userId);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find ALL learners by site ID
     *
     * @param integer $siteId
     * @param array $status
     * @return ArrayCollection
     */
    public function findAllBySiteId ($siteId, $status = [ 'new', 'active', 'inactive' ])
    {
        // create query
        $dql = "SELECT learner, employment, site, settings
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                WHERE learner.status IN (:status) AND site.siteId = :siteId
                GROUP BY learner.userId
                ORDER BY learner.firstName ASC, learner.lastName ASC";
        $params = [
            'status' => $status,
            'siteId' => $siteId
        ];

        // execute query
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');
        $result = $repository->fetchCollection($dql, $params);

        return $result;
    }

    /**
     * Find one new learner by site ID and Status
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function getNewLearnerBySiteId ($siteId)
    {
        // a manually added learner will have the following 'status'
        $status = 'active';

        // create query
        $dql = "SELECT learner, employment, site, settings
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                WHERE learner.status IN (:status) AND site.siteId = :siteId
                ORDER BY learner.userId DESC";
        $params = [
        'status' => $status,
        'siteId' => $siteId
        ];

        // execute query
        $r = false;
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');
        $results = $repository->fetchCollection($dql, $params);

        $index = 0;
        foreach ($results as $result) {
            if ($index > 0) { break; }
            if ($index == 0) { $r = $result; }
            $index++;
        }
        return $r;
    }

    public function countLearners($siteId)
    {
        $status = [ 'new', 'active', 'inactive' ];
        $repository = $this->learnerRepository();
        $dql[] = "SELECT COUNT(learner.userId)
            FROM Savvecentral\Entity\Learner learner
            LEFT JOIN learner.site site
            WHERE learner.status IN (:learnerStatus)
            AND site.siteId = :siteId";

        $params['learnerStatus'] = $status;
        $params['siteId'] = $siteId;

        // execute query
        return $repository->fetchScalar($dql, $params);

    }

    /**
     * Find ALL learners by site ID with LIMIT (used for API loading)
     *
     * @param integer $siteId
     * @param integer $limit
     * @param array   $status
     * @return ArrayCollection
     */
    public function findAllBySiteIdWithLimit ($siteId, $limit, $status = [ 'new', 'active', 'inactive' ], $activityIds = [])
    {
        $repository = $this->learnerRepository();
        $entityManager = $this->getEntityManager();

//        WHERE distribution.activity = :activityId AND distribution.learner = :learnerId")
        $qbActivities = $entityManager->createQueryBuilder()
            ->from('Savvecentral\Entity\Distribution', 'distribution')
            ->select('dLearner.userId')
            ->join('distribution.learner', 'dLearner')
            ->where('distribution.activity in (:activityIds)')
            ->setParameter('activityIds', $activityIds);

        // create query(s)
        if ($limit == 0) {
            // this is to reduce the initial load time of a directory listing
            $qb = $repository->createQueryBuilder('learner')
                ->select('learner, employment')
                ->leftJoin('learner.site', 'site')
                ->leftJoin('learner.employment', 'employment')
                ->where('learner.status IN (:status) AND site.siteId = :siteId')
                ->orderBy('learner.firstName', 'ASC')
                ->setParameter('status', $status)
                ->setParameter('siteId', $siteId);

        } else {
            // this will load everything after the intial directory load
            $qb = $repository->createQueryBuilder('learner')
                ->select('learner, employment')
                ->leftJoin('learner.site', 'site')
                ->leftJoin('learner.employment', 'employment')
                ->where('learner.status IN (:status) AND site.siteId = :siteId')
                ->orderBy('learner.firstName', 'ASC')
                ->setFirstResult(0)
                ->setMaxResults($limit)
                ->setParameter('status', $status)
                ->setParameter('siteId', $siteId);
        }

        if (count($activityIds) > 0) {
            $qb
                ->andWhere(
                    $qb->expr()->in('learner.userId', $qbActivities->getDQL())
                )
                ->setParameter('activityIds', $activityIds);
        }


        // execute query
        return $repository->fetchCollection($qb);

    }

    /**
     * COUNT ALL learners by site ID with LIMIT (used for API loading)
     *
     * @param integer $siteId
     * @param array   $status
     * @return ArrayCollection
     */
    public function countAllBySiteId ($siteId, $status = [ 'new', 'active', 'inactive' ])
    {
        $repository = $this->learnerRepository();
        $limit = 5000;
        // create query
    /*    $dql = "SELECT learner, employment, site, settings
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                WHERE learner.status IN (:status) AND site.siteId = :siteId";

        $params = [
            'status' => $status,
            'siteId' => $siteId
        ]; */

        $qb = $repository->createQueryBuilder('learner')
        ->select('learner, employment, site, settings')
        ->leftJoin('learner.site', 'site')
        ->leftJoin('learner.employment', 'employment')
        ->leftJoin('learner.settings', 'settings')
        ->where('learner.status IN (:status) AND site.siteId = :siteId')
        ->orderBy('learner.firstName', 'ASC')
        ->setFirstResult(0)
        ->setMaxResults($limit)
        ->setParameter('status', $status)
        ->setParameter('siteId', $siteId);

        $results = $repository->fetchCollection($qb);

        // execute query
  //      $entityManager = $this->getEntityManager();
   //     $repository = $entityManager->getRepository('Savvecentral\Entity\Learner');
  //      $result = $repository->fetchOne($dql, $params);
   //     $results = $repository->fetchCollection($dql, $params);
  //      return $result[1];

        return count($results);
    }

    /**
     * Fetch ALL learners by site ID
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function fetchAllBySiteId ($siteId)
    {
        $repository = $this->learnerRepository();
        $entityManager = $this->getEntityManager();
        $params = [];

        // learner status
        $status = [ 'new', 'active', 'inactive' ];

        // create query
        $dql[] = "SELECT
                	learner.userId AS learner_id,
                	learner.firstName AS learner_first_name,
                	learner.lastName AS learner_last_name,
                	CONCAT(learner.firstName,' ',learner.lastName) AS learner_name,
                	learner.telephone AS learner_telephone,
                	learner.mobileNumber AS learner_mobile_number,
                	learner.email AS learner_email,
                	learner.streetAddress AS learner_street_address,
			      	learner.suburb AS learner_suburb,
			      	learner.postcode AS learner_postcode,
			      	learner.state AS learner_state,
			      	learner.country AS learner_country,
                    learner.username AS learner_password,
                	CONCAT(learner.streetAddress,' ', learner.suburb,' ', learner.postcode, ' ', learner.state, ' ', learner.country) AS learner_address,
                	learner.gender AS learner_gender,
                	learner.cpdId AS learner_cpd_id,
                	learner.cpdNumber AS learner_cpd_number,
                	learner.referrer AS learner_referrer,
                	learner.note AS learner_note,
                	learner.subscription AS learner_subscription,
                	learner.status AS learner_status,
			learner.agent AS agent_code,
                	employment.employmentId AS employment_id,
					employment.employmentType AS employment_type,
					employment.position AS employment_position,
                    employment.location AS employment_location,
					employment.startDate AS employment_start_date,
					employment.endDate AS employment_end_date,
					employment.costCentre AS employment_cost_centre,
					employment.manager AS employment_manager,
                	site.siteId AS site_id,
                	site.name AS site_name
                FROM Savvecentral\Entity\Learner learner
                LEFT JOIN learner.site site
                LEFT JOIN learner.employment employment
                LEFT JOIN learner.settings settings
                WHERE learner.status IN (:learnerStatus)
                AND site.siteId = :siteId
                GROUP BY learner.userId";
        $params['learnerStatus'] = $status;
        $params['siteId'] = $siteId;

        // order by
        $dql[] = "ORDER BY learner.firstName, learner.lastName";

        // execute query
        $results  = $repository->fetchScalar($dql, $params);

        return $results;
    }

    /**
     * Find ALL ACTIVE learners by site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllActiveBySiteId ($siteId)
    {
        $learners = $this->findAllBySiteId($siteId);
        $learners = $learners->filter(function($item){
        	return in_array($item['status'], ['new', 'active']);
        });

        return $learners;
    }

    /**
     * Find ALL INACTIVE learners by site ID
     *
     * @param integer $siteId
     * @return ArrayCollection
     */
    public function findAllInactiveBySiteId ($siteId)
    {
        $learners = $this->findAllBySiteId($siteId);
        $learners = $learners->filter(function  ($item)
        {
            return $item['status'] === 'inactive';
        });

        return $learners;
    }

    /**
     * Find ALL DELETED learners by site ID
     *
     * @param $siteId
     * @return ArrayCollection
     */
    public function findAllDeletedBySiteId ($siteId)
    {
        $status = [ 'deleted' ];

        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->leftJoin('learner.site', 'site')
            ->leftJoin('learner.employment', 'employment')
            ->leftJoin('learner.settings', 'settings')
            ->select('learner, site, employment, settings')
            ->add('orderBy', 'learner.firstName ASC, learner.lastName ASC')
            ->where('learner.status IN(:status) AND site.siteId = :siteId')
            ->setParameter('status', $status)
            ->setParameter('siteId', $siteId);

        // execute query
        $result = $repository->fetchCollection($qb);

        return $result;
    }

    /**
     * Find all learners given a user ID
     *
     * @param array $userIds
     * @return ArrayCollection
     */
    public function findAllByUserId ($userIds)
    {
        if (is_string($userIds) || is_numeric($userIds)) {
            $userIds = (array) $userIds;
        }

        $repository = $this->learnerRepository();

        // create  query
        $qb = $repository->createQueryBuilder('learner')
            ->leftJoin('learner.site', 'site')
            ->leftJoin('learner.employment', 'employment')
            ->leftJoin('learner.settings', 'settings')
            ->select('learner, site, employment, settings')
            ->where('learner.userId IN (:userIds)')
            ->setParameter('userIds', $userIds);

        // execute query
        $result = $repository->fetchCollection($qb);

        return $result;
    }

    /**
     * Find an employment detail for a given employment ID and options site ID
     *
     * @param $employmentId
     * @param null $siteId
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findEmploymentByEmploymentId ($employmentId, $siteId = null)
    {
        $routeMatch = $this->routeMatch();
        if (null == $siteId) {
            $siteId = $routeMatch->getParam('site_id');
        }

        $repository = $this->employmentRepository();

        // create query
        $qb = $repository->createQueryBuilder('employment')
            ->select('employment')
            ->join('employment.learner', 'learner')
            ->addSelect('learner')
            ->join('learner.site', 'site', 'WITH', 'site.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->addSelect('site')
            ->andWhere('employment.employmentId = :employmentId')
            ->setParameter('employmentId', $employmentId);

        // we only want ONE record
        $qb->setMaxResults(1);

        // execute query
        $result = $repository->fetchOne($qb);
        return $result;
    }

    /**
     * Finds a duplicate email address excluding an optional given user ID
     *
     * @param $email
     * @param null $userId
     * @param null $siteId
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findDuplicateEmail ($email, $userId = null, $siteId = null)
    {
        $routeMatch = $this->routeMatch();
        if (null == $siteId) {
            $siteId = $routeMatch->getParam('site_id');
        }

        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner')
            ->leftJoin('learner.site', 'site', 'WITH', 'site.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->andWhere('learner.email = :email')
            ->setParameter('email', $email);

        if ($userId) {
            $qb->andWhere('learner.userId != :userId')
                ->setParameter('userId', $userId);
        }

        // we only want ONE record
        $qb->setMaxResults(1);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find a duplicate mobile number excluding an optional given user ID
     *
     * @param string $mobileNumber
     * @param integer $userId
     * @param integer $siteId
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findDuplicateMobileNumber ($mobileNumber, $userId = null, $siteId = null)
    {
        $routeMatch = $this->routeMatch();
        if (null == $siteId) {
            $siteId = $routeMatch->getParam('site_id');
        }

        $repository = $this->learnerRepository();

        // create query
        $qb = $repository->createQueryBuilder('learner')
            ->select('learner')
            ->leftJoin('learner.site', 'site', 'WITH', 'site.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->andWhere('learner.mobileNumber = :mobileNumber')
            ->setParameter('mobileNumber', $mobileNumber);

        if ($userId) {
            $qb->andWhere('learner.userId != :userId')
                ->setParameter('userId', $userId);
        }

        // we only want ONE record
        $qb->setMaxResults(1);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Find a duplicate employment ID and exclude a learner given their user ID
     *
     * @param string $employmentId
     * @param integer $userId
     * @param integer $siteId
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findDuplicateEmployment ($employmentId, $userId = null, $siteId = null)
    {
        $routeMatch = $this->routeMatch();
        if (null == $siteId) {
            $siteId = $routeMatch->getParam('site_id');
        }

        $repository = $this->employmentRepository();

        // create query
        $qb = $repository->createQueryBuilder('employment')
            ->select('employment')
            ->join('employment.learner', 'learner')
            ->addSelect('learner')
            ->join('learner.site', 'site', 'WITH', 'site.siteId = :siteId')
            ->setParameter('siteId', $siteId)
            ->addSelect('site')
            ->andWhere('employment.employmentId = :employmentId')
            ->setParameter('employmentId', $employmentId);

        if ($userId) {
            $qb->andWhere('learner.userId != :userId')
                ->setParameter('userId', $userId);
        }

        // we only want ONE record
        $qb->setMaxResults(1);

        // execute query
        $result = $repository->fetchOne($qb);

        return $result;
    }

    /**
     * Creates an Entity\Site instance from the Doctrine ORM EntityManager
     *
     * @param integer $siteId
     * @return \Entity\Site
     */
    public function createSiteEntity ($siteId)
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
    }

    /**
     * Create a new employment entity
     *
     * @param integer $userId
     * @return Entity\Employment $employment
     */
    public function createEmploymentEntity ($userId)
    {
        $entityManager = $this->getEntityManager();
        $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $userId);

        // new employment entity
        $employment = new Entity\Employment();
        $employment['learner'] = $learner;

        // save
        $entityManager->persist($employment);
        $entityManager->flush($employment);

        return $employment;
    }

    /**
     * Login ONE learner
     * @param string $identity
     * @param string $password
     * @return LearnerService
     */
    public function login ($identity, $password)
    {
        $serviceManager = $this->getServiceLocator();
        $entityManager = $this->getEntityManager();

        $routeMatch = $this->routeMatch();
        $siteId = $routeMatch->getParam('site_id');
    //    $platformId = $routeMatch->getParam('platform_id'); // not used at the moment = this existed in the OLD ambiguous leaner query

        // check if this is an ADFS enabled site
        /* @var $site \Savvecentral\Entity\Site */
        $site = $serviceManager->get('Site\Entity');
        if ($site['enable_adfs_login'] == 1) {
            // get the options for this ADFS connection
            $this->adfsOptions = $this->getAdfsOptions($siteId);
            // so we know to run the ADFS login function further down
            $ldapEnabled       = true;
            // in case we need to create a 'new' account for this user
            $createNewLdapUser = false;
            // we need to check if the 'identity' is a full email address or just the 'username' part
            if (strpos($identity, "@") !== false) {
                // nothing to do
            } else {
                // get the 'options' for this ADFS setup and add the AD domain to this username
                // - we need this to 'query' against out users table
                foreach ($this->adfsOptions as $key => $arr) {
                    $domain = $arr['accountDomainName'];
                    if (strlen($domain)) {
                        $identity = $identity . "@" . $domain;
                        break;
                    }
                }
            }
        } else {
            $ldapEnabled = false;
        }

        // due to an issue with cross-site login we'll create a separate login check for Savve Admin
        if ($identity == 'savve@savv-e.com.au') {

            // create query to find the learner details using the identity
            $repository = $this->learnerRepository();
            $qb = $repository->createQueryBuilder('learner')
                ->leftJoin('learner.employment', 'employment')
                ->leftJoin('learner.site', 'site')
                ->leftJoin('site.platform', 'platform')
                ->select('learner, site, platform')
                ->where('learner.email = :identity AND site.siteId IS NULL')
                ->setParameter('identity', $identity)
                ->setMaxResults(1);

        } else {

            // create query to find the learner details using the identity
            $repository = $this->learnerRepository();
            $qb = $repository->createQueryBuilder('learner')
                ->leftJoin('learner.employment', 'employment')
                ->leftJoin('learner.site', 'site')
                ->leftJoin('site.platform', 'platform')
                ->select('learner, site, platform')
                ->where('(learner.email = :identity OR learner.mobileNumber = :identity OR employment.employmentId = :identity OR learner.username = :identity) AND site.siteId = :siteId')
                ->setParameter('identity', $identity)
                ->setParameter('siteId', $siteId)
                ->setMaxResults(1);
        }

        // !! this 'where' statement below was far to ambiguous - it was not respecting 'strict' Site ID requirements and was allowing cross-site logins to 'ALL' users !!
        // ->where('(learner.email = :identity OR learner.mobileNumber = :identity OR (employment.employmentId = :identity AND site.siteId = :siteId)) AND ((site.siteId = :siteId OR site.siteId = NULL) OR platform.platformId = :platformId)')
        // ->setParameter('platformId', $platformId)
        // execute query
        /* @var $learner \Savvecentral\Entity\Learner */
        $learner = $repository->fetchOne($qb);

        // set a default of false for this value
        $createNewLdapUser = false;

        // if learner is not found, then do not proceed with a 'normal' login - try the LDAP
        if (!$learner) {
            if ($ldapEnabled == true) {
                $createNewLdapUser = true;
            } else {
                throw new Exception\UnauthorisedException('The username could not be found. Please check that you entered the correct details or contact Administration for further assistance.');
            }
        }

        // if a learner exists and the learner status is not active, then do not proceed
        if ($learner && in_array($learner['status'], [ 'inactive', 'expired', 'deleted' ])) {
            throw new Exception\UnauthorisedException(sprintf('The learner is currently inactive or does not exists on the system, please contact the administrator.'));
        }

        // trigger event listeners
        $this->triggerListeners(new Event(Event::EVENT_LOGIN . '.pre', $this, [ 'learner' => $learner ]));

        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');

        $ldapAuthFailed  = false; // use this to check if we had a successful LDAP login further down
        $ldapAuthSuccess = false; // this gets set to true if we have a successful LDAP login
        $useLdapAuth     = false; // if we attempt an LDAP auth this will be set to true

        if ($ldapEnabled && $identity != 'savve@savv-e.com.au') {
            $useLdapAuth = true;

            $result = $this->ldapLogin($identity, $password, $site);
            $messages = $result->getMessages();

            if ($result->getCode() <= 0) {
                $ldapAuthFailed  = true;
                if ($createNewLdapUser == true) {
                    throw new Exception\UnauthorisedException('The username entered failed to authenticate or does not exist. Please check that you entered the correct details or contact Administration for further assistance.');
                }

            } else {
                $ldapAuthSuccess = true;
                // Todo: this needs revisiting as its !! NOT !! working !! It should initialise the users session - it currently does NOT !!
      //          $authentication->getStorage()->write($result->getIdentity());
            }

            $log_path = LOG_PATH . DIRECTORY_SEPARATOR . "ldap.log";

            $logger = new Logger;
            $writer = new LogWriter($log_path);
            $logger->addWriter($writer);
            $filter = new LogFilter(Logger::DEBUG);
            $writer->addFilter($filter);

            foreach ($messages as $i => $message) {
                 $message = str_replace("\n", "\n  ", $message);
                 $logger->debug("Ldap: $i: $message");
            }

            if ($ldapEnabled == true && $createNewLdapUser == true && $ldapAuthSuccess == true) {

                // create the new learner
                $learner = new Entity\Learner();
                $learner['user_id']      = null;
                $learner['status']       = 'active';
                $learner['username']     = $identity;
                if (isset($this->ldapMemberEmail) && strpos($this->ldapMemberEmail, '@') !== false) {
                    $learner['email']    = $this->ldapMemberEmail;
                } else {
                    $learner['email']    = $identity;
                }
                $learner['new_password'] = $password;
                $arr                     = explode(" ", $this->ldapMemberDisplayName);
                $firstName               = $arr[0];
                $lastName                = $arr[count($arr)-1]; // in case a middle name is thrown in there
                $learner['first_name']   = $firstName;
                $learner['last_name']    = $lastName;

                // link site relation
                $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
                $learner['site'] = $site;

                // save the learner entity to the repository
                $entityManager->persist($learner);
                $entityManager->flush($learner);

                if (isset($this->ldapMemberGroups)) {
                    if (isset($learner['user_id'])) {

                        /* @var $groupService \Group\Service\GroupService */
                        $groupService        = $serviceManager->get('Group\Service');
                        $groups = $groupService->findAllActiveGroupsBySiteId($siteId);

                        /* @var $groupLearnerService \Group\Learner\Service\GroupLearnerService */
                        $groupLearnerService = $serviceManager->get('Group\Learner\Service');

                        // iterate through the groups and try to match the group names in out ldapMember group array
                        foreach ($groups as $group) {
                            if ( in_array($group['name'], $this->ldapMemberGroups) ) {
                                // get the group id
                                $groupLearnerService->addLearnersToGroups(array($learner['user_id']), array($group['group_id']));
                            }
                        }
                    }
                }
            }
        }

        /* TODO: Until I can get the verified LDAP users session to fire correctly - just run the login again -- this will then populate the session correctly */
        if ($learner) {

            $identity = $learner['user_id'];

            /* @var $adapter \Authentication\Doctrine\Adapter\ObjectRepository */
            $adapter = $authentication->getAdapter();
            $adapter->setIdentity($identity);
            $adapter->setCredential($password);

            // authenticate
            $result = $authentication->authenticate();
            if ($result->getCode() <= 0) {
                throw new Exception\UnauthorisedException(sprintf('Cannot login learner. Learner either does not have correct identity, credentials or impersonation is not allowed.'));
            }

        } else {
            // if there is no learner at this stage throw an exception
            throw new Exception\UnauthorisedException(sprintf('No unauthorised access permitted!!.'));
        }

        // if logged in, check if password is unencrypted
        // extract the learner details
    //    $userId = $learner['user_id'];
        $encryptionMode = $learner['encryption_mode'];
    //    $salt = $learner['salt'];

        // if password is plaintext, then encrypt it and update record in the database
        if ($encryptionMode === 'plaintext') {
            $learner['new_password'] = $password;

            // persist and then save
            $entityManager->persist($learner);
            $entityManager->flush($learner);
        }

        // trigger event listeners
        $this->triggerListeners(new Event(Event::EVENT_LOGIN, $this, [ 'learner' => $learner ]));

        return $learner;
    }

    public function ldapLogin($identity, $password, $site) {

        $ldap   = new Ldap\Ldap(['optReferrals' => false]);
        $cannon = substr($identity, 0, strpos($identity, "@"));
        $authed = false;
        $usersGroups = array();
        try {
            foreach ($this->adfsOptions as $key => $arr) {

                // this prevents the loop from checking all 'servers' after 'auth' succeeds
                if ($authed == true) { continue; }

                // these may actually not be required !!
            //    ldap_set_option($ldap->getResource(), LDAP_OPT_PROTOCOL_VERSION,3);

                $ldap->setOptions($arr);
                $ldap->bind($identity, $password);
                $acctName = $ldap->getCanonicalAccountName($identity);

                // this prevents the consecutive options iterations from running if an authenticated state has been achieved
                if (isset($acctName)) {
                    $authed = true;
                }

                // ToDo: unfortunately this returns the OU not the DC values
          //      $baseDn = $ldap->getBaseDn(); /* should be like: "dc=savv-e,dc=com" */;
                // for now we'll just bust up the returned 'Account Name'
                $name   = explode("@", $acctName);
                $name   = explode(".", $name[1]);
                $baseDn = "dc=$name[0],dc=$name[1]";

                // this filter seems to do the trick for getting the user Real Name from the AD !!
                $filter="(|(userprincipalname=$cannon*))";

                // we really need the 'userprincipalname' as that contains their 'directory system generated email' which is username + local DSN which must match the '$identity'
                $attrs = array("ou", "sn", "userprincipalname", "displayname", "mail");

                // this will return just the details for the 'bound' account
                $account = $ldap->searchEntries($filter, $baseDn, Ldap\Ldap::SEARCH_SCOPE_SUB, $attrs);

                // we need to keep track of their display name in case its a first login
                if ($account[0]['userprincipalname'][0] == $identity) {
                    $this->ldapMemberDisplayName = $account[0]['displayname'][0];
                    // save their real email address whenever possible
                    if (isset($account[0]['mail'])) {
                        $this->ldapMemberEmail = $account[0]['mail'][0];
                    }
                }

                if (isset($site['enable_adfs_group_sync']) && $site['enable_adfs_group_sync'] == true) {
                    // get the groups this user has in the AD
                    $fltr = "(&(objectCategory=group)(member=CN=" . $this->ldapMemberDisplayName . ",OU=SBSUsers,OU=Users,OU=MyBusiness,DC=savv-e,DC=com))";
                    $attr = array("ou", "sn", "cn");
                    $groups = $ldap->searchEntries($fltr, $baseDn, Ldap\Ldap::SEARCH_SCOPE_SUB, $attr);

                    if (count($groups) >= 1) {
                        foreach ($groups as $group) {
                            $groupDN = $group['dn'];
                            $usersGroups[] = explode('=', substr($groupDN, 0, strpos($groupDN, ',')))[1];
                        }
                        $this->ldapMemberGroups = $usersGroups;
                    }
                }
            }

            $messages[0] = '';
            $messages[1] = '';
            $messages[] = "$cannon authentication successful";

            return new AuthenticationResult(AuthenticationResult::SUCCESS, $identity, $messages);

        } catch (\Exception $zle) {

              $code = AuthenticationResult::FAILURE;
              $message = $zle->getMessage();
              $messages[] = "$identity authentication failed: $message";
              if ($zle->getCode() == 49) {
                  // invalid credentials
                  $code = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
                  if (strpos($message, "data 775") !== false) {
                      $messages[] = "Additional Information: Error code '775' indicates this Active Directory account has been locked out: $identity";
                  }
              }
              return new AuthenticationResult($code, $identity, $messages);
        }
    }

    /**
     * Methods get the ADFS options using the supplied site ID
     */
    private function getAdfsOptions($siteId) {

        $entityManager = $this->getEntityManager();

        // create query
        $dql = "SELECT adfs
                FROM Savvecentral\Entity\SiteAdfs adfs
                LEFT JOIN adfs.site site
                WHERE site.siteId = :siteId";

        $params['siteId'] = $siteId;

        $repository = $entityManager->getRepository('Savvecentral\Entity\SiteAdfs');
        $result = $repository->fetchOne($dql, $params);

        $object = json_decode($result['adfs']);
        // we need to convert into an array and also remove any options that the Ldap class cannot use
        $options = array();
        $arr = array();
        foreach ($object as $server => $obj) {
            foreach ($obj as $key => $val) {
                // cant use this
                if ($key == 'option_id') { continue; }
                $arr[$key] = $val;
            }
            $options[$server] = $arr;
        }
        return $options;
    }

    /**
     * Impersonate a learner
     *
     * @param string $authenticationToken
     * @return Entity\Learner
     * @throws \Exception
     */
    public function impersonate ($authenticationToken)
    {
        try {
	        $repository = $this->learnerRepository();

	        // find the learner using authentication token
	        $learner = $this->findOneByAuthenticationToken($authenticationToken);

	        // if not found, throw an error
	        if (!$learner) {
	            throw new Exception\UnauthorisedException(sprintf('Cannot impersonate learner. Learner either does not exists or impersonation is not allowed. ' . $authenticationToken));
	        }

	        // get the learner's login identity
	        $identity = $learner['user_id'];
	        $password = $learner['password'];

	        $serviceManager = $this->getServiceLocator();

	        /* @var $authentication \Zend\Authentication\AuthenticationService */
	        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');


	        /* @var $adapter \Authentication\Doctrine\Adapter\ObjectRepository */
	        $adapter = $authentication->getAdapter();
	        $adapter->setIdentity($identity);
	        $adapter->setCredential($password);

	        // authenticate
	        $result = $authentication->authenticate();
	        if ($result->getCode() <= 0) {
	            throw new Exception\UnauthorisedException(sprintf('Cannot impersonate learner. Learner either does not have correct identity, credentials or impersonation is not allowed.'));
	        }

	        // trigger event listeners
	        $this->triggerListeners(new Event(Event::EVENT_IMPERSONATE, $this, [ 'learner' => $learner ]));

	        return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * SSO login
     *
     * @param string $token
     * @param integer $siteId
     * @return Entity\Learner
     * @throws \Exception
     */
    public function ssoLogin ($token, $siteId)
    {
        try {
            $tokens = explode('.', $token);
            if (count($tokens) != 3) {
                throw new Exception\UnexpectedValueException('Wrong number of token segments');
            }
            list($headb64, $bodyb64, $cryptob64) = $tokens;
            if (null === ($payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64)))) {
                throw new Exception\UnexpectedValueException('Invalid claims encoding');
            }
            if (empty($payload->email) || empty($payload->iss)) {
                throw new Exception\UnexpectedValueException('Missing required fields in claims');
            }
            $serviceManager = $this->getServiceLocator();
            $apiToken = $serviceManager->get('SavvecentralApi\Service\Core')->findOneSSOApiByPublicKeyAndSiteId($payload->iss, $siteId);
            if (empty($apiToken)) {
                throw new Exception\UnexpectedValueException('Invalid value provided in claims');
            }
            $payload = JWT::decode($token, $apiToken->getApiPrivateKey(), array('HS256', 'HS384', 'HS512'));
            $learner = $this->findOneByEmail($payload->email);
            if (empty($learner)) {
                throw new Exception\UnauthorisedException('Cannot login learner. Learner either does not have correct identity, credentials or sso is not allowed.');
            }

            $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');

            $adapter = $authentication->getAdapter();
            $adapter->setIdentity($learner['user_id']);
            $adapter->setCredential($learner['password']);

            // authenticate
            $result = $authentication->authenticate();
            if ($result->getCode() <= 0) {
                throw new Exception\UnauthorisedException(sprintf('Cannot login learner. Learner either does not have correct identity, credentials or sso is not allowed.'));
            }
            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_LOGIN, $this, [ 'learner' => $learner ]));
            return $learner;
        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Log out the current logged in user
     *
     * @return LearnerService
     */
    public function logout ()
    {
        $serviceManager = $this->getServiceLocator();

        // authentication service
        /* @var $authentication \Zend\Authentication\AuthenticationService */
        $authentication = $serviceManager->get('Zend\Authentication\AuthenticationService');

        if ($identity = (string) $authentication->getIdentity()) {
            $repository = $this->learnerRepository();
            $learner = $repository->find($identity);

            /* @var $storage \Authentication\Storage\Session */
            $storage = $authentication->getStorage();

            // clear session
            $authentication->clearIdentity();

            $learnerSession = new SessionContainer('learner');
            $sessionManager = $learnerSession->getManager();
            $sessionManager->forgetMe();
            $sessionManager->getStorage()
                ->clear();

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_LOGOUT, $this, [ 'learner' => $learner ]));
        }

        return $this;
    }

    /**
     * Check if a user is currently logged in
     *
     * @return boolean
     */
    public function isLoggedIn ()
    {
        $serviceManager = $this->getServiceLocator();

        /* @var $authenticationService \Zend\Authentication\AuthenticationService */
        $authenticationService = $serviceManager->get('Zend\Authentication\AuthenticationService');

        // check if user is logged in
        return $authenticationService->hasIdentity() && (string) $authenticationService->getIdentity() ? true : false;
    }

    /**
     * Register a new learner
     *
     * @param array $data
     * @return LearnerService
     * @throws \Exception
     */
    public function register ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            // check if user_id or learner_id is provided
            if (!(isset($data['site_id']) && $data['site_id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot register learner, site ID was not provided.'));
            }

            // ensure that the email does not exist
            $emailExists = $this->findDuplicateEmail($data['email']);
            if ( isset($emailExists) && count($emailExists)) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot register learner, due to a data exists conflict.'));
            }

            $entityManager = $this->getEntityManager();

            // create the new learner
            $learner = new Entity\Learner();
            $learner = Stdlib\ObjectUtils::hydrate($data, $learner);
            $learner['user_id'] = null;
            $learner['status'] = 'active';

            // link site relation
            $siteId = $data['site_id'];
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
            $learner['site'] = $site;

            if (isset($data['agent_code'])) {
                $learner['agent'] = $data['agent_code'];
            }

            // save the learner entity to the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // add the employment entity, if it exists
            if (isset($data['employment_id']) && $data['employment_id']) {
                $employment = new Entity\Employment();
                $employment = Stdlib\ObjectUtils::hydrate($data, $employment);
                $employment['employment_id'] = isset($data['employment_id']) ? trim($data['employment_id']) : null;
                $employment['learner'] = $learner;

                // persist the employment entity
                $entityManager->persist($employment);
                $entityManager->flush($employment);
            }

            // add learner to a group if selected
            if (isset($data['group_id']) && $data['group_id']) {

                $groupLearnerService = $this->getServiceLocator()->get('Group\Learner\Service');
                $groupLearnerService->addLearnersToGroups(array($learner['user_id']), array($data['group_id']), array());
            }

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_REGISTERED, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Inserts new learner into the database
     *
     * @param array $data
     * @return LearnerService
     * @throws \Exception
     */
    public function create ($data)
    {
        try {
            $entityManager = $this->getEntityManager();

            if (!($data instanceof Entity\Learner)) {

	            // create the new  learner
                $siteId = $data['site_id'];
                unset($data['site_id']);

	            $learner = new Entity\Learner();
	            $learner->populate($data);

	            // insert the site entity
	            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);
	            $learner['site'] = $site;
            }
            else {
                $learner = $data;
            }
            $learner['user_id'] = null;
            $learner['status'] = 'active';

            // persist the learner entity
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // add the employment entity, if it exists
            if (isset($data['employment_id'])) {
                $employment = new Entity\Employment();
                $employment->populate($data);
                $employment['employment_id'] = isset($data['employment_id']) ? trim($data['employment_id']) : null;
                $employment['learner'] = $learner;

                // add the employment association to learner
                $learner['employment'] = $employment;

                // persist the employment entity
                $entityManager->persist($employment);
                $entityManager->flush($employment);
            }
            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_CREATED, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update an existing learner in the database
     *
     * @param array $data
     * @return Entity\Learner
     * @throws \Exception
     */
    public function update ($data)
    {
        try {
            $data = Stdlib\ObjectUtils::extract($data);

            // check if user_id or learner_id is provided
            if (!(isset($data['learner_id']) && $data['learner_id']) && !(isset($data['user_id']) && $data['user_id'])) {
                throw new Exception\InvalidArgumentException(sprintf('Cannot update learner details, learner ID was not provided.'));
            }

            $serviceManager = $this->getServiceLocator();
            $entityManager = $this->getEntityManager();
            $learnerId = isset($data['learner_id']) && $data['learner_id'] ? $data['learner_id'] : (isset($data['user_id']) && $data['user_id'] ? $data['user_id'] : null);

            // get the learner instance
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);
            $learner = Stdlib\ObjectUtils::hydrate($data, $learner);
            // persist the learner repository
            $entityManager->persist($learner);

            // add the employment entity, if it exists
            if (isset($data['employment_id'])) {
                $employment = $entityManager->getReference('Savvecentral\Entity\Employment', $learnerId);
                if ($employment && $learner['employment']) {
                    $employment = Stdlib\ObjectUtils::hydrate($data, $employment);
                } else {
                    $employment = new Entity\Employment();
                    $employment->populate($data);
                    $employment['employment_id'] = isset($data['employment_id']) ? trim($data['employment_id']) : null;
                    $employment['learner'] = $learner;
                    // add the employment association to learner
                    $learner['employment'] = $employment;
                }
                // persist the employment entity
                $entityManager->persist($employment);
            }

            // save to the repository
            $entityManager->flush();

            // save the user settings
            $options = $this->options;
            $fields = $options->getFields();
            foreach ($fields as $field) {
                if (isset($learner[$field])) {
                    $setting = $this->saveSetting($learnerId, $field, $learner[$field]);
                }
            }

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_UPDATED, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
	 * Save learner settings
	 * @param integer $learnerId
	 * @param string $name
	 * @param mixed $value
	 * @return Entity\LearnerSettings
     */
    public function saveSetting ($learnerId, $name, $value)
    {
        $entityManager = $this->getEntityManager();
        $repository = $this->learnerSettingsRepository();

        // check if setting exists
        $dql = "SELECT settings
                FROM Savvecentral\Entity\LearnerSettings settings
                LEFT JOIN settings.learner learner
                WHERE learner.userId = :learnerId
                AND settings.name = :name";
        $params = [
            'learnerId' => $learnerId,
            'name' => $name
        ];
        $query = $entityManager->createQuery($dql)
            ->setParameters($params);
        $setting = $query->getOneOrNullResult();

        // if settings does not exists, create a new one
        if (!$setting) {
            $setting = new Entity\LearnerSettings();
            $setting['name'] = $name;
        }
		$learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);
        $setting['learner'] = $learner;
        $setting['value'] = $value;

        // persist
        $entityManager->persist($setting);
        $entityManager->flush($setting);

        return $setting;
    }

    /**
     * Change and update password
     *
     * @param $learnerId
     * @param $newPassword
     * @return bool|\Doctrine\Common\Proxy\Proxy|null|object
     * @throws \Exception
     */
    public function changePassword ($learnerId, $newPassword)
    {
        try {
            // save to the repository
            $entityManager = $this->getEntityManager();
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);

            // set new password
            $learner['new_password'] = $newPassword;

            // save to repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_CHANGE_PASSWORD, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Forgot password request
     *
     * @param string $identity
     */
    public function forgotPassword ($identity)
    {
        try {
    //        $serviceManager = $this->getServiceLocator();
            $entityManager = $this->getEntityManager();
            $repository = $this->learnerRepository();

            // find the learner by email or mobile_number or employment_id
            $dql = "SELECT learner
                    FROM Savvecentral\Entity\Learner learner
                    LEFT JOIN learner.site site
                    LEFT JOIN learner.employment employment
                    WHERE learner.email = :identity OR learner.mobileNumber = :identity OR employment.employmentId = :identity";
            $params['identity'] = $identity;

            // execute query
            $learner = $repository->fetchOne($dql, $params);

            // if not found, do not continue
            if (!$learner) {
                throw new LearnerException\LearnerNotFoundException(sprintf('Cannot reset password for the learner. Learner does not exist in the system'), null, null);
            }

            if (!isset($learner['email']) || empty($learner['email'])) {
                throw new LearnerException\LearnerNotFoundException(sprintf('Cannot reset password for the learner. Learner does not have an email address'), null, null);
            }

            // set the reset password token values
            $options = $this->options;
            $expiryLength = $options->getPasswordTokenExpiry();
            $learner['password_token'] = Stdlib\SecurityUtils::generateToken(24);
            $learner['password_token_expiry'] = new \DateTime(date('Y-m-d H:i:s', time() + $expiryLength));

            // update in the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_FORGOT_PASSWORD, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Resets/changes the password after reset password request
     *
     * @param $learnerId
     * @param $newPassword
     * @return Entity\Learner|\bool|\Doctrine\Common\Proxy\Proxy|null|object
     * @throws \Exception
     */
    public function resetPassword ($learnerId, $newPassword)
    {
        try {
            $entityManager = $this->getEntityManager();

            // find and update learner password (password is encrypted in the entity)
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);
            $learner['new_password'] = $newPassword;
            $learner['password_token'] = null;
            $learner['password_token_expiry'] = null;
            $learner['authentication_token'] = Stdlib\SecurityUtils::generateRandomString(32);

            // save to the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_RESET_PASSWORD_SUCCESS, $this, [ 'learner' => $learner ]));

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Deletes the learner from the platform
     *
     * @param integer $learnerId
     */
    public function delete ($learnerId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);

            // change status to deleted
            $learner['status'] = 'deleted';

            //For us to continue reusing the identity elsewhere we are creating a new identity for the user .
            //created a deleted identity
            $newIdentity = sprintf("%s.isDeleted@savvecentral.com.au",$learnerId);
            if($learner['email']){
                //identity is an email
                $learner['email'] = $newIdentity;
            }
            if($learner['mobile_number']){
                $learner['mobile_number'] = $learnerId;
            }
            if(($learner['employment'] && $learner['employment']['employment_id'])){
                $learner['employment']['employment_id'] = $newIdentity;
            }

            // save to the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            // get the group repository - thid learner must be removed from groups
            // get the groups this learner is in
            $repository = $entityManager->getRepository('Savvecentral\Entity\Groups');
            $qb = $repository->createQueryBuilder('groups')
                ->select('groups, groupLearners, learner')
                ->leftJoin('groups.groupLearners', 'groupLearners')
                ->leftJoin('groupLearners.learner', 'learner')
                ->where('learner.userId = :userId')
                ->setParameter('userId', $learnerId)
                ->add('orderBy', 'groups.name ASC');
            // execute query
            $results = $repository->fetchCollection($qb);
            $groupIds = array();
            foreach ($results as $row) {
                $groupIds[] = $row['group_id'];
            }

            $learnerIds = array();
            $learnerIds[] = $learnerId;
            // remove the learner from any groups...
            $repository = $entityManager->getRepository('Savvecentral\Entity\GroupLearners');
            $results = $repository->createQueryBuilder('groupLearners')
                ->delete('Savvecentral\Entity\GroupLearners', 'groupLearners')
                ->where('groupLearners.learner IN (:learnerIds) AND groupLearners.group IN (:groupIds)')
                ->setParameter('learnerIds', $learnerIds)
                ->setParameter('groupIds', $groupIds)
                ->getQuery()
                ->execute();
            $entityManager->clear();

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Activates a learner
     *
     * @param integer $learnerId
     */
    public function activate ($learnerId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);

            // trigger PRE BULK UPLOAD event listener
            $this->triggerListeners(new Event(Event::EVENT_ACTIVATE . '.pre', $this, [ 'learner' => $learner ]));

            // change status to active
            $learner['status'] = 'active';

            // save to the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Deactivates a learner
     *
     * @param integer $learnerId
     */
    public function deactivate ($learnerId)
    {
        try {
            $entityManager = $this->getEntityManager();
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);

            // change status to active
            $learner['status'] = 'inactive';

            // save to the repository
            $entityManager->persist($learner);
            $entityManager->flush($learner);

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Bulk upload by CSV array
     *
     * @param array $data
     * @param integer $siteId
     * @param integer $allowed (number of licensed learners allowed)
     * @param integer $current (number of active users within the current site)
     * @return LearnerService
     * @throws \Exception
     */
    public function bulkUpload (array $data, $siteId, $allowed, $current)
    {
        try {
            // convert each item's row heading to underscore format
            $data = Stdlib\ArrayUtils::underscoreFieldNormaliseValue($data);
            // catch empty uploads and return an exception that can be used to return a nice message
            $totalUploadedData = count($data);
            if ($totalUploadedData == 1 || $totalUploadedData == 0) {
                $item = (isset($data[0])) ? $data[0] : false;
                if (!(isset($item['email']) && strlen($item['email']) >= 5) && !(isset($item['mobile_number']) && strlen($item['mobile_number']) >= 8) && !(isset($item['employment_id']) && strlen($item['employment_id']) >= 1)) {
                    throw new \Exception("Empty CSV Uploaded");
                }
            }
            $entityManager = $this->getEntityManager();
            $repository = $this->learnerRepository();
            $routeMatch = $this->getRouteMatch();

            // trigger PRE BULK UPLOAD event listener
            $this->triggerListeners(new Event(Event::EVENT_BULK . '.pre', $this, [ 'learners' => $data ]));

            // find ALL learners in the current site
            $repository = $this->learnerRepository();
            $dql = "SELECT
                    learner.userId AS learner_id,
                    learner.email AS email,
                    learner.mobileNumber AS mobile_number,
                    employment.employmentId AS employment_id

                    FROM Savvecentral\Entity\Learner learner
                    LEFT JOIN learner.site site
                    LEFT JOIN learner.employment employment
                    WHERE site.siteId = :siteId
                    ORDER BY learner.firstName, learner.lastName";
            $params['siteId'] = $siteId;
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery($dql)
                ->setParameters($params);
            $learners = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);

            // get the instance of the current site entity
            $site = $entityManager->getReference('Savvecentral\Entity\Site', $siteId);

            // create the new learners from the data input
            $successful = [];
            $existing   = [];
            $badInfo    = [];
            $licenceExceeded = false;

            function validateEmail($email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    return false;
                }
                return true;
            }

            foreach ($data as $k => $item) {
                $meta = [
                    'key' => $k,
                    'item' => $item,
                ];

                $existsCorrectEmail = isset($item['email']) && strlen($item['email']) >= 5;
                $existsCorrectPhone = isset($item['mobile_number']) && is_numeric($item['mobile_number']) && strlen($item['mobile_number']) >= 8;
                $existsCorrectEmployee = isset($item['employment_id']) && strlen($item['employment_id']) >= 1;
                $existsAndNotValidEmail = strlen($item['email']) > 0 && validateEmail($item['email']) == false;

                if (!$existsCorrectEmail && !$existsCorrectPhone && !$existsCorrectEmployee) {
                    $badInfo[] = $meta + [
                            'error' => 'Missing identify field: email(length > 4) or phone(length>7) or employment_id',
                        ];
                    continue;
                }

                if ($existsAndNotValidEmail) {
                    // capture the email
                    $badInfo[] = $meta + [
                            'error' => 'Incorrect email used "'.$item['email'].'" (comments and whitespace folding and dotless domain names are not supported)',
                        ];
                    continue;
                }

                // check if the current item already exists in the learner repository
                $found = array_filter($learners, function  ($a) use( $item)
                {
                    return ((isset($item['email']) && $item['email']) && (isset($a['email']) && $a['email']) && trim($item['email']) === trim($a['email']))
                    		|| ((isset($item['mobile_number']) && $item['mobile_number']) && (isset($a['mobile_number']) && $a['mobile_number']) && trim($item['mobile_number']) === trim($a['mobile_number']))
                    	    || ((isset($item['employment_id']) && $item['employment_id']) && (isset($a['employment_id']) && $a['employment_id']) && trim($item['employment_id']) === trim($a['employment_id']));
                });
                $found = current($found);

                // if found, then this is an update
                if ($found) {
                    // if password is set, then change password
                    if(isset($item['password']) && $item['password'] != 'N/A' && strlen($item['password']) >= 6) {
                        $item['salt'] = null;
                    	$item['encryption_mode'] = 'plaintext';
                    	unset($item['new_password']);
                    } else {
                        unset($item['password']);
                    }

                    // if status is 'deleted' ensure the email address is also 'mangled'
                    if (isset($item['status']) && $item['status'] == 'deleted') {
                        // mangle it!!
                        $item['email'] = sprintf("%s.isDeleted@savvecentral.com.au",$found['learner_id']);
                    }

                    if (isset($found['learner_id'])) {
                        $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $found['learner_id']);
                        $learner = Stdlib\ObjectUtils::hydrate($item, $learner);
                    }
                }

                // else, create a new user
                else {
                    // ensure that the 'email' really doesn't exist
                    if (isset($item['email']) && strlen($item['email'])) {
                        $dql = "SELECT learner
                            FROM Savvecentral\Entity\Learner learner
                            WHERE learner.email = :email
                            ORDER BY learner.firstName, learner.lastName";

                        $params = array('email' => $item['email']);

                        $query = $entityManager->createQuery($dql)
                            ->setParameters($params);
                        $learner = $query->getOneOrNullResult();

                        if (isset($learner) || count($learner)) {
                            $badInfo[] = $meta + [
                                    'error' => 'Learner exists "'.$item['email'].'"'
                             ];
                            continue;
                        }
                    }
                    if ($current < $allowed) {
                        // clear any 'status' values
                        $item['status'] = null;
                        //    unset($item['status']);
                        // set the password as plaintext
                        $emptyPass = "password143";
                        $item['password'] = isset($item['password']) ? $item['password'] : (isset($item['new_password']) ? $item['new_password'] : $emptyPass);
                        $item['salt'] = null;
                        $item['encryption_mode'] = 'plaintext';
                        unset($item['new_password']);

                        // save the learner details
                        $learner = new Entity\Learner();
                        $learner = Stdlib\ObjectUtils::hydrate($item, $learner);
                        $learner['site'] = $site;
                        //$learner['status'] = 'new';
                        $learner['status'] = 'active';
                        $current++;

                    } else {
                        $licenceExceeded = true;
                        $badInfo[] = $meta + [
                                'error' => 'Licence Exceeded '
                            ];
                        continue;
                    }
                }

                try {
                    // persist
                    $entityManager->persist($learner);
                    $entityManager->flush($learner);
                } catch (\Throwable $throwable) {
                    if (strpos($throwable->getMessage(), 'Duplicate entry') !== false && strpos($throwable->getMessage(), 'UK_USER_EMAIL') !== false) {
                        $badInfo[] = $meta + [
                                'error' => 'Duplicate email has been found'
                            ];
                    } else {
                        $badInfo[] = $meta + [
                                'error' => $throwable->getMessage()
                            ];
                    }
                    continue;
                }


                // check if employment details already exists
            //    $employmentId = isset($item['employment_id']) && trim($item['employment_id']) ? trim($item['employment_id']) : null;
                $learnerId = $learner['user_id'];
	            $employment = $entityManager->getRepository('Savvecentral\Entity\Employment')->findOneBy(['learner' => $learnerId]);

                // if employment does not exists, create a new one
	            if (!$employment) {
		            $employment = new Entity\Employment();
	            }

	            $employment = Stdlib\ObjectUtils::hydrate($item, $employment);
	            $employment['start_date'] = (isset($item['start_date']) && !empty($item['start_date'])) ? Stdlib\DateUtils::convertUStoUTC($item['start_date']) : null;
	            $employment['end_date'] = (isset($item['end_date']) && !empty($item['end_date'])) ? Stdlib\DateUtils::convertUStoUTC($item['end_date']) : null;
	            $employment['learner'] = $learner;

	            // persist
	            $entityManager->persist($employment);
	            $entityManager->flush($employment);

                // add to collection
                $successful[] = $learner;
                $learners[] = $item;
            }

            if (count($badInfo) >= 1) {
                $incorrectLearners = " Learner(s): ";
                foreach($badInfo as $arr) {
                    $incorrectLearners .= $arr['error']." on line ".($arr['key']+2).". ".PHP_EOL;
                }
                $count = count($badInfo);
                if ($licenceExceeded == true) {
                    throw new \Exception(sprintf("Error: Company Learner Licence Exceeded! CSV Uploaded with %s error(s)! Unable to update/add the following learner(s)-> %s", $count, $data));
                } else {
                    throw new \Exception(sprintf("CSV Uploaded with %s error(s)! Unable to update/add the following learner(s)-> %s", $count, $incorrectLearners));
                }
            }

            // trigger event listeners
            $this->triggerListeners(new Event(Event::EVENT_BULK, $this, [ 'learners' => $successful, 'existing' => $existing ]));
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Upload the learner's profile photo
     *
     * @param integer $learnerId
     * @param array|\Traversable $data
     * @return Entity\Learner
     * @throws \Exception
     */
    public function uploadProfilePhoto ($learnerId, $data)
    {
        try {
            // success
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save learner profile photo
     *
     * @param integer $learnerId
     * @param string $uri
     * @return Entity\Learner
     * @throws \Exception
     */
    public function saveProfilePhoto ($learnerId, $uri)
    {
        try {
            $name = 'profile_picture';
            $entityManager = $this->getEntityManager();
            $repository = $entityManager->getRepository('Savvecentral\Entity\LearnerSettings');
            $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $learnerId);

            // check if profile photo already exists in the repository
            $dql = "SELECT setting
                    FROM Savvecentral\Entity\LearnerSettings setting
                    LEFT JOIN setting.learner learner
                    WHERE learner.userId = :learnerId AND setting.name = :name";
            $params['learnerId'] = $learnerId;
            $params['name'] = $name;
            $query = $entityManager->createQuery($dql)
                ->setParameters($params);
            $setting = $query->getOneOrNullResult(); // do not retrieve the cached version
            if (!$setting) {
                $setting = new Entity\LearnerSettings();
                $setting['name'] = $name;
                $setting['learner'] = $learner;
            }
            $setting['value'] = $uri;

            // persist to repository
            $entityManager->persist($setting);
            $entityManager->flush($setting);
            $entityManager->clear('Savvecentral\Entity\LearnerSettings');

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the learner's profile photo
     *
     * @param integer $learnerId
     * @return Entity\Learner
     * @throws \Exception
     */
    public function removeProfilePhoto ($learnerId)
    {
        try {
            // retrieve the profile photo filename
            $learner = $this->findOneByUserId($learnerId);
            $profilePhoto = $learner['profile_picture'];

            $options = $this->options;

            // the location of the learner's profile photo
            $filePath = $options->getUploadPath() . DIRECTORY_SEPARATOR . $learnerId . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $profilePhoto;

            // delete the file
            if (!file_exists($filePath)) {
                throw new Exception\FileNotFoundException(sprintf('File "%s" not found', $profilePhoto));
            }
            Stdlib\FileUtils::delete($filePath);

            // remove the profile photo from the learner repository
            $learner['profile_picture'] = null;

            // update learner data
            $learner = $this->update($learner);

            return $learner;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save learner employment details
     *
     * @param Entity\Employment $entity
     * @return LearnerService
     */
    public function saveEmployment ($data)
    {
        $userId = $data['user_id'];
        $entityManager = $this->getEntityManager();
        $learner = $entityManager->getReference('Savvecentral\Entity\Learner', $userId);

        if ($learner['employment'] == null) {
            $employment = new Entity\Employment();
        } else {
            $employment = $entityManager->getReference('Savvecentral\Entity\Employment', $userId);
        }
        $employment['learner'] = $learner;
        $employment = Stdlib\ObjectUtils::hydrate($data, $employment);

        $entityManager->persist($employment);
        $learner['employment'] = $employment;
        $entityManager->persist($learner);
        $entityManager->flush();

		// load the learner details
        $learner = $this->findOneByUserId($data['user_id']);

        // trigger event listeners
        $this->triggerListeners(new Event(Event::EVENT_UPDATED, $this, [ 'learner' => $learner ]));
    }

    public function attachDefaultListeners ()
    {
        $eventManager = $this->getEventManager();
        $eventManager->addIdentifiers(__NAMESPACE__);
        $eventManager->setEventClass(get_class(new Event()));
    }

    /**
     * Generate a password token string
     *
     * @param int $length
     * @return string
     */
    public function generatePasswordToken ($length = 24)
    {
        return Stdlib\SecurityUtils::generateToken($length);
    }

    /**
     * Sets the reset password values to the entity
     * @param Entity\Learner $entity
     */
    protected function setResetPassword (Entity\Learner $entity)
    {
        /* @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $this->getServiceLocator();

        /* @var $options \Learner\Service\Options */
        $options = $serviceManager->get('Learner\Options');
        $expiryLength = $options->getPasswordTokenExpiry();

        // create password token
        $entity['password_token'] = Stdlib\SecurityUtils::generateToken(24);
        $entity['password_token_expiry'] = new \DateTime(date('Y-m-d H:i:s', time() + $expiryLength));

        return $this;
    }

    /**
     * Retuen the Learners impersonate key
     *
     * @param in $userId
     * @return string
     */
    public function getLearnerImpersonateKey( $userId ) {
        if ($userId) {
            $entityManager = $this->getEntityManager();

            $dql = "SELECT learner.authenticationToken
                FROM Savvecentral\Entity\Learner learner
                WHERE learner.userId = :learnerId";
            $params['learnerId'] = $userId;

            $query = $entityManager->createQuery($dql)
                ->setParameters($params);
            $key = $query->getOneOrNullResult();

            return $key;
        }
    }

    /**
     * Create new authentication token
     *
     * @return string
     */
    public function createAuthenticationToken ()
    {
        return Stdlib\SecurityUtils::generateRandomString();
    }

    /**
     * Get Learner Doctrine EntityRepository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learnerRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Learner');
    }

    /**
     * Get learner settings doctrine entityrepository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function learnerSettingsRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\LearnerSettings');
    }

    /**
     * Get the employment repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function employmentRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Employment');
    }

    /**
     * Get the platform repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function platformRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Platform');
    }

    /**
     * Get the site repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function siteRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Site');
    }

    /**
     * Get the agent repository
     *
     * @return \Savve\Doctrine\Repository\AbstractRepository
     */
    public function agentRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\Agent');
    }
}
