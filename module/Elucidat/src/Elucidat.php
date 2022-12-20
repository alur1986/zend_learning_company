<?php

namespace Elucidat\Elucidat;

use Elucidat\Elucidat\Client\ClientInterface;
use Elucidat\Elucidat\Client\Unirest as Client;
use Savvecentral\Entity;
use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Savve\Doctrine\Service\AbstractService;

class Elucidat extends AbstractService {

    /**
     * Elucidat end point
     *
     * @var string
     */
    protected $url;

    protected $client;

    /**
     * Constructor
     *
     * @param ClientInterface $client
     */
    public function __construct (ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $accountId
     *
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findOneAccountByAccountId ($accountId)
    {
        $repository = $this->elucidatAccountRepository ();

        $qb = $repository->createQueryBuilder ('elucidatAccount')->leftJoin ('elucidatAccount.site', 'site')
                         ->select ('elucidatAccount,site')->where ('elucidatAccount.id = :accountId')
                         ->setParameter ('accountId', $accountId);

        $result = $repository->fetchOne ($qb);
        return $result;
    }

    /**
     * Get all Elucidat Accounts registered on Savvecentral
     */
    public function findAllAccounts ()
    {
        $repository = $this->elucidatAccountRepository ();

        $qb = $repository->createQueryBuilder ('elucidatAccount')
        		->add ('orderBy', 'elucidatAccount.status ASC, elucidatAccount.elucidatPublicKey DESC, elucidatAccount.companyEmail ASC');

        $results = $repository->fetchCollection ($qb);
        return $results;
    }

    /**
     * Get all Elucidat Accounts registered on Savvecentral
     */
    public function findAllAuthorsByAccountId ($accountId)
    {
        $repository = $this->elucidatUserRepository ();

        $qb = $repository->createQueryBuilder ('elucidatUser')->leftJoin ('elucidatUser.user', 'user')
                         ->leftJoin ('elucidatUser.account', 'account')->leftJoin ('account.site', 'site')
                         ->select ('elucidatUser,account,site')->where ('account.id = :accountId')
                         ->andWhere ('user.status IN (:userStatus)')->setParameter ('accountId', $accountId)
                         ->setParameter ('userStatus', ['active', 'new', 'enabled'])->add ('orderBy', 'user.email');


        $results = $repository->fetchCollection ($qb);
        return $results;
    }

    /**
     * @param $accountId
     *
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findOneAuthorByAuthorId ($authorId)
    {
        $repository = $this->elucidatUserRepository ();

        $qb = $repository->createQueryBuilder ('elucidatUser')->leftJoin ('elucidatUser.user', 'user')
                         ->leftJoin ('elucidatUser.account', 'account')->leftJoin ('account.site', 'site')
                         ->select ('elucidatUser,account,site')->where ('elucidatUser.id = :authorId')
                         ->setParameter ('authorId', $authorId);

        $result = $repository->fetchOne ($qb);
        return $result;
    }

    /**
     * @param $userId
     *
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function findOneAuthorByUserId ($userId)
    {
        $repository = $this->elucidatUserRepository ();

        $qb = $repository->createQueryBuilder ('elucidatUser')
                         ->leftJoin ('elucidatUser.user', 'user')
                         ->leftJoin ('elucidatUser.account', 'account')
                         ->leftJoin ('account.site', 'site')
                         ->select ('elucidatUser,account,site')
                         ->where ('user.userId = :userId')
                         ->setParameter ('userId', $userId);

        $result = $repository->fetchOne ($qb);
        return $result;
    }

    /**
     * @param $elucidatCustomerCode
     *
     * @return \Savve\Doctrine\Entity\AbstractEntity
     */
    public function getSiteByCustomerCode ($customerCode)
    {
    	$repository = $this->elucidatAccountRepository ();

    	$qb = $repository->createQueryBuilder ('elucidat')
    	->leftJoin ('elucidat.site', 'site')
    	->select ('elucidat, site')
    	->where ('elucidat.elucidatCustomerCode = :customerCode')
    	->setParameter ('customerCode', $customerCode);


    	$result = $repository->fetchOne ($qb);
    	return $result;
    }

    /**
     * Create a elucidat Account entry in Savvecentral Database
     *
     * @param $module
     *
     * @return Stdlib\stdClass|Entity\ElucidatAccount
     * @throws \Exception
     */
    public function createElucidatAccount ($module)
    {
        try {
            $data = Stdlib\ObjectUtils::extract ($module);

            //entity manager
            $entityManager = $this->getEntityManager ();
            $site = $entityManager->getReference ('Savvecentral\Entity\Site', $data['site_id']);

            //create elucidat entry
            $elucidatAccount = new Entity\ElucidatAccount();
            $elucidatAccount = Stdlib\ObjectUtils::hydrate ($data, $elucidatAccount);
            $elucidatAccount['site'] = $site;

            //Save in repositry
            $entityManager->persist ($elucidatAccount);
            $entityManager->flush ($elucidatAccount);
            $entityManager->clear ();
            return $elucidatAccount;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Unlink / Delete a elucidat Account entry in Savvecentral Database
     *
     * @param $module
     *
     * @return Stdlib\stdClass|Entity\ElucidatAccount
     * @throws \Exception
     */
    public function unlinkElucidatAccount ($module)
    {
    	try {

    		$data = Stdlib\ObjectUtils::extract ($module);
    		$customerCode 	= $data['elucidat_customer_code'];
    		$siteId 		= $data['site_id'];

    		//entity manager
    		$entityManager=$this->getEntityManager();

    		//create query
    		$dql[] = "DELETE
                  FROM Savvecentral\Entity\ElucidatAccount account
                  WHERE account.elucidatCustomerCode IN (:customerCode)
    				AND account.site = :siteId";

    		$params = [
    		'customerCode' 	=> $customerCode,
    		'siteId' 		=> $siteId
    		];
    		$dql = implode(" ",$dql);
    		$result = $entityManager->createQuery($dql)
    		->setParameters($params)
    		->getScalarResult();

    		return $result;

    	} catch (\Exception $e) {
    		throw $e;
    	}
    }


    /**
     * Update one account
     *
     * @param Entity\ElucidatAccount $account
     *
     * @return Entity\ElucidatAccount
     * @throws \Exception
     */
    public function updateAccount (Entity\ElucidatAccount $account)
    {
        try {
            $entityManager = $this->getEntityManager ();

            // save in repository
            $entityManager->persist ($account);
            $entityManager->flush ($account);
            $entityManager->clear ();

            return $account;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete an elucidat user
     * @param Entity\ElucidatUser $author
     */
    public function deleteElucidatUser (Entity\ElucidatUser $author)
    {
        try {
            $entityManager = $this->getEntityManager();

            // remove from repository
            $entityManager->remove($author);
            $entityManager->flush($author);
            $entityManager->clear();
        }
        catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @param Entity\ElucidatUser $account
     *
     * @return Entity\ElucidatUser
     * @throws \Exception
     */
    public function updateAccountAuthor (Entity\ElucidatUser $author)
    {
        try {
            $entityManager = $this->getEntityManager();

            // save in repository
            $entityManager->persist($author);
            $entityManager->flush($author);
            $entityManager->clear();

            return $author;
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Allow the author to have elucidat access
     * @param $author
     *
     * @return mixed
     */
    public function activateAuthor($author)
    {
        $author->setHasElucidatAccess(true);
        $entityManager = $this->getEntityManager();
        // save in repository
        $entityManager->persist($author);
        $entityManager->flush($author);
        $entityManager->clear();

        return $author;
    }

    /**
     * DissAllow the author to have elucidat access
     * @param $author
     *
     * @return mixed
     */
    public function deactivateAuthor($author)
    {
        $author->setHasElucidatAccess(false);
        $entityManager = $this->getEntityManager();
        // save in repository
        $entityManager->persist($author);
        $entityManager->flush($author);
        $entityManager->clear();
        return $author;
    }

    /**
     * Allow the account to have elucidat access
     * @param $account
     *
     * @return mixed
     */
    public function activateAccount($account)
    {
    	$account->setStatus('active');
    	$entityManager = $this->getEntityManager();
    	// save in repository
    	$entityManager->persist($account);
    	$entityManager->flush($account);
    	$entityManager->clear();
    	return $account;
    }

    /**
     * DissAllow the account to have elucidat access
     * @param $account
     *
     * @return mixed
     */
    public function deactivateAccount($account)
    {
    	$account->setStatus('inactive');
    	$entityManager = $this->getEntityManager();
    	// save in repository
    	$entityManager->persist($account);
    	$entityManager->flush($account);
    	$entityManager->clear();
    	return $account;
    }

    /**
     * Create a new Elucidat record
     *
     * @param string $module Record type
     * @param array  $fields Record fields
     *
     * @return Returns array if successful, false if not
     */
    public function create($module, $fields)
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }

        $data = $module;
        if($module instanceof \ArrayObject){
            $data = $module->getArrayCopy();
        }

        $response = $this->client->post('reseller/create_new_account',$data);

        // convert stdClass to array
        if (is_object($response)) {
            $response = json_decode(json_encode($response), true);
        }

        return $response;
    }


    /**
     * Update an existing Elucidat record
     *
     * @param string $module Record type
     * @param array  $fields Record fields
     *
     * @return Returns array if successful, false if not
     */
    public function update($module, $fields)
    {
        $data =  Stdlib\ObjectUtils::extract($module);

        $response = $this->client->update($data);

        // convert stdClass to array
        if (is_object($response)) {
            $response = json_decode(json_encode($response), true);
        }

        return $response;
    }

    /**
     * As a reseller create a public key

     * @param $account
     *
     * @return mixed
     */
    public function createPublicKeysFor($account)
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }

        $data =  [
            'customer_code' => $account['elucidat_customer_code'],
            'name' => sprintf('Savvecentral-%s',$account['account_id'])
        ];

        $response = $this->client->post('reseller/create_new_key',$data);

        // convert stdClass to array
        if (is_object($response)) {
            $response = json_decode(json_encode($response), true);
        }

        return $response;
    }
    /**
     * Create a author within the elucidat account
     * @param $account
     */
    public function createAuthorForElucidatAccount($account,$data)
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }
        return $this->client->createAuthorForElucidatAccount($account,$data);
    }

    /**
     * Delete an author from elucidat
     * @param $author
     *
     * @return mixed
     */
    public function deleteAuthorForElucidatAccount($author)
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }
        return $this->client->deleteAuthorForElucidatAccount($author);
    }

    /**
     * Retrieve any reseller web hooks
     */
    public function retrieveResellerWebhooks()
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }

        return $this->client->get('event');
    }

    public function retrieve()
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }

        return $this->client->get('reseller/list_clients');
    }


    /**
     * @param $account_id
     *
     * @return mixed
     * @throws \Exception
     */
    public function retrieveAuthors($account)
    {
        // connect if not yet connected
        if (!$this->client->check()) {
            $this->client->connect();
        }


        if(is_int($account)){
            $accountId = $account;
        }
        else{
            $accountId = $account['id'];
        }

        return $this->client->retrieveAuthors($account);

    }

    /**
     * Find the association between the elucidat account and the Savvecentral account
     */
    public function findAssociatedAccounts($customerCodes)
    {
        $entityManager=$this->getEntityManager();


        //create query
        $dql[] = "SELECT account.companyName as company_name,
                         account.companyEmail as company_email,
                         account.firstName as first_name,
                         account.lastName as last_name,
                         account.telephone as telephone,
                         account.address1 as address1,
                         account.address2 as address2,
                         account.postcode as postcode,
                         account.country as country,
                         account.elucidatCustomerCode as customer_code,
                         account.elucidatPublicKey as public_key,
                         site.url as site_url,
                         site.name as site_name

                  FROM Savvecentral\Entity\ElucidatAccount account
                  LEFT JOIN account.site site
                  WHERE account.elucidatCustomerCode IN (:customerCodes)
                  ";

        $params = [
            'customerCodes' => $customerCodes
        ];
        $dql = implode(" ",$dql);
        $results = $entityManager->createQuery($dql)
            ->setParameters($params)
            ->useResultCache(true, (60 * 60 * 15), md5(Stdlib\StringUtils::dashed($dql) . serialize($params)))
            ->getScalarResult();

        return $results;
    }
    /**
     * Get Method for Token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->client->getToken();
    }

    /**
     * Set Method for Token
     *
     * @param mixed $token
     *
     * @return Unirest
     */
    public function setToken($token)
    {
        $this->client->setToken($token);
        return $this;
    }

    /**
     * Get Method for ClientId
     *
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client->getClientId();
    }

    /**
     * Set Method for ClientId
     *
     * @param mixed $clientId
     *
     * @return Unirest
     */
    public function setClientId($clientId)
    {
        $this->client->setClientId($clientId);
        return $this;
    }

    /**
     * Get Method for ClientSecret
     *
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client->getClientSecret();
    }

    /**
     * Set Method for ClientSecret
     *
     * @param mixed $clientSecret
     *
     * @return Unirest
     */
    public function setClientSecret($clientSecret)
    {
        $this->client->setClientSecret($clientSecret);
        return $this;
    }

    /**
     * Get Method for GrantType
     *
     * @return mixed
     */
    public function getGrantType()
    {
        return $this->client->getGrantType();
    }

    /**
     * Set Method for GrantType
     *
     * @param mixed $grantType
     *
     * @return Unirest
     */
    public function setGrantType($grantType)
    {
        $this->client->setGrantType($grantType);
        return $this;
    }

    /**
     * Get Method for Platform
     *
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->client->getPlatform();
    }

    /**
     * Set Method for Platform
     *
     * @param mixed $platform
     *
     * @return Unirest
     */
    public function setPlatform($platform)
    {
        $this->client->setPlatform($platform);
        return $this;
    }

    /**
     * Get Method for Username
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->client->getUsername();
    }

    /**
     * Set Method for Username
     *
     * @param mixed $username
     *
     * @return Unirest
     */
    public function setUsername($username)
    {
        $this->client->setUsername($username);
        return $this;
    }

    /**
     * Get Method for Password
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->client->getPassword();
    }

    /**
     * Set Method for Password
     *
     * @param mixed $password
     *
     * @return Unirest
     */
    public function setPassword($password)
    {
        $this->client->setPassword($password);
        return $this;
    }

    /**
     * Get Method for Url
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->client->getUrl();
    }

    /**
     * Set Method for Url
     *
     * @param mixed $url
     *
     * @return Unirest
     */
    public function setUrl($url)
    {
        $this->client->setUrl($url);
        return $this;
    }

    /**
     * Set up the base launch url
     * @param $url
     *
     * @return $this
     */
    public function setBaseUrl ($url)
    {
        $this->client->setBaseUrl ( $url);
        return $this;
    }
    /**
     * Get Method for ProjectUrl
     *
     * @return mixed
     */
    public function getProjectUrl()
    {
        return $this->client->projectUrl;
    }

    /**
     * Set Method for ProjectUrl
     *
     * @param mixed $projectUrl
     *
     * @return Unirest
     */
    public function setProjectUrl($projectUrl)
    {
        $this->client->setProjectUrl($projectUrl);
        return $this;
    }

    /**
     * Checks if the current Site has Access to Elucidat
     *
     * @param integer $siteId
     *
     * @return boolean
     */
    public function checkAccountAccess($siteId)
    {
    	if ($siteId) {
    		try {
    			$repository = $this->elucidatAccountRepository ();

    			$qb = $repository->createQueryBuilder ('elucidatAccount')
    					->select ('elucidatAccount.id')
    					->where ('elucidatAccount.site = :siteId AND elucidatAccount.status = :status')
    					->setParameter ('siteId', $siteId)
    					->setParameter ('status', 'active');

    			$results = $repository->fetchOne ($qb);
    			return $results;
    		}
    		catch(\Exception $e) {
    			throw $e;
    		}
    	}
    }

    /**
     * Checks if the current User has Access to Elucidat
     *
     * @param integer $siteId
     *
     * @return boolean
     */
    public function checkUserAccess($userId, $accountId)
    {
    	if ($userId) {
    		try {
    			$repository = $this->elucidatUserRepository ();

    			$qb = $repository->createQueryBuilder ('elucidatUser')
    			->select ('elucidatUser.id')
    			->where ('elucidatUser.user = :userId AND elucidatUser.account = :accountId AND elucidatUser.hasElucidatAccess = :hasAccess' )
    			->setParameter ('userId', $userId)
    			->setParameter ('accountId', $accountId)
    			->setParameter ('hasAccess', true);

    			$results = $repository->fetchOne ($qb);
    			return $results;
    		}
    		catch(\Exception $e) {
    			throw $e;
    		}
    	}
    }


    /**
     * Generate the Elucudat Launch URL / HREF to show within the /mytools page
     *
     * @param string $authorEmail	Author Email in Savvecentral\Entity\ElucidatUser
     * @param string $companyName	Companyname in Savvecentral\Entity\ElucidatAccount
     * @param string $publicKey		Key in Savvecentral\Entity\ElucidatAccount
     *
     * @return string Elucidat SSO API launch link
     */
    public function generateLaunchLink($authorEmail, $companyName, $publicKey)
    {
    	// connect if not yet connected
    	if (!$this->client->check()) {
    		$this->client->connect();
    	}
    	return $this->client->getLaunchLink( $authorEmail, $companyName, $publicKey);

    }

    /**
     * Test connection
     */
    public function test()
    {
    //    if(!$this->client){
    //        printr(__FILE__." (line) ".__LINE__);
    //        printr("Unable to locate client");
     //   }

        if (!$this->client->check()) {
            $this->client->connect();
        }
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\Savve\Doctrine\Repository\EntityRepository
     */
    public function elucidatAccountRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\ElucidatAccount');
    }
    /**
     * @return \Doctrine\ORM\EntityRepository|\Savve\Doctrine\Repository\EntityRepository
     */
    public function elucidatUserRepository ()
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->getRepository('Savvecentral\Entity\ElucidatUser');
    }
}