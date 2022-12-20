<?php

namespace Elucidat\Elucidat\Client;

use Savve\Stdlib\Exception\Exception;
use \Unirest\Request;

class Unirest implements
    ClientInterface
{
    protected $token;

    protected $clientId;

    protected $clientSecret;

    protected $grantType;

    protected $platform;

    protected $username;

    protected $password;

    protected $url;
    protected $projectUrl;


    protected $baseUrl;

    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_POST = 'POST';

    /**
     * Constructor
     */
    public function __construct()
    {
        \Unirest\Request::jsonOpts(true);
    }


    /**
     * Create a new access token (nonce)
     *
     * @param $api_url
     * @param $consumer_key
     * @param $consumer_secret
     *
     * @return mixed
     */
    public function newAccessToken($api_url, $consumer_key, $consumer_secret)
    {
        if (!$this->token) {
            $auth_headers = $this->auth_headers($consumer_key);
            unset($auth_headers['oauth_nonce']);

            //Make a request to elucidat for a nonce...any url is fine providing it doesnt already have a nonce
            $json = $this->_call($auth_headers, array(), self::METHOD_GET, $api_url, $consumer_secret);

            if(isset($json['response']['nonce'])){
                $this->token = $json['response']['nonce'];
            }
        }

        return $this->token;
    }


    /**
     * Retrieve a Elucidat token
     */
    public function connect()
    {
        //get a new token
        $token = $this->newAccessToken($this->projectUrl, $this->clientId, $this->clientSecret);
        if (!$token) {
            return false;
        }
    }


    /**
     * Check if the token is set
     *
     * @return bool
     */
    public function check()
    {
        if (!$this->token) {
            return false;
        }

        return true;
    }

    public function get($endpoint, array $parameters = [])
    {
       if (!$this->check()) {
            $this->connect();
        }

        $url = $this->url;
        if($endpoint && !empty($endpoint)){
            $url = $this->url.$endpoint;
        }

        $headers = $this->auth_headers($this->clientId, $this->token);
        $fields = [];//array('simulation_mode'=>'simulation');
        $result = $this->_call($headers, $fields, self::METHOD_GET, $url, $this->clientSecret);

        return $result['response'];
    }

    /**
	 * Fetches the API SSO launch link which will be displayed on the /mytools page
	 *
	 * @param string $authorEmail
	 * @param string $companyName
	 * @param string $publicKey  Accounts elucidat_public_key
	 * @return string Elucidat SSO launch link
     */
    public function getLaunchLink($authorEmail, $companyName, $publicKey)
    {
    	$secret = $this->clientSecret;
    	// endpoint matching the recommendation in the Eluciudat documentation
    	$endPoint = 'projects';
    	$nonce = $this->newAccessToken(sprintf('%s%s',$this->url,$endPoint), $publicKey, $secret);
		// base URL for Elucidat Single Sign Ons
    	$url = $this->baseUrl.'single_sign_on/login';
		// page (URI) at Elucidat to redirect to after the SSO has completed successfully
    	$redirectUrl ='projects';

    	$params = array('oauth_consumer_key' => $publicKey,
    			'oauth_nonce' => $nonce,
    			'oauth_signature_method' => 'HMAC-SHA1',
    			'oauth_timestamp' => time(),
    			'oauth_version' => '1.0',
    			'email' => $authorEmail,
    			'redirect_url' => $redirectUrl);

    	$params['oauth_signature'] = $this->build_signature($secret, $params, 'GET', $this->baseUrl.'single_sign_on/login');
    	$request = $this->build_base_string($params, '&');

    	$link = $url . '?' . $request;

    	return $link;
    }

    public function post($endpoint, array $parameters = [])
    {
        if (!$this->check()) {
            $this->connect();
        }

        $url = $this->url;
        if($endpoint && !empty($endpoint)){
            $url = $this->url.$endpoint;
        }

        $headers = $this->auth_headers($this->clientId, $this->token);
        $fields = array_merge([],$parameters); //array('simulation_mode'=>'simulation')
        $result = $this->_call($headers, $fields, self::METHOD_POST, $url, $this->clientSecret);
        return $result['response'];
    }

    public function put($endpoint, array $parameters = [])
    {

    }

    public function delete($endpoint, array $parameters = [])
    {

    }


    /**
     * Make an update call to elucidat and update account.
     * This request is signed with Savv-e's private keys
     * @param $data
     *
     * @return array
     */
    public function update($data){

        //newAccessToken($api_url, $consumer_key, $consumer_secret)
        $secret = $this->clientSecret;
        $key = (isset($data['elucidat_public_key']) && $data['elucidat_public_key']) ?$data['elucidat_public_key'] : $this->clientId;
        $endPoint = 'account/update';

        $nonce = $this->newAccessToken(sprintf('%s%s',$this->url,$endPoint),$key,$secret);

        $headers = $this->auth_headers($key, $nonce);

        $result = $this->_call($headers, $data, 'POST', sprintf('%s%s',$this->url,$endPoint), $secret);

        return $result;
    }

    /**
     * Retrieve all the authors from elucidat
     * @param $account_id
     *
     * @return mixed
     */
    public function retrieveAuthors($data)
    {
        $secret = $this->clientSecret;
        $key = (isset($data['elucidat_public_key']) && $data['elucidat_public_key']) ?$data['elucidat_public_key'] : $this->clientId;
        $endPoint = 'authors';

        $nonce = $this->newAccessToken(sprintf('%s%s',$this->url,$endPoint),$key,$secret);

        $headers = $this->auth_headers($key, $nonce);

        $fields = [];
        $result = $this->_call($headers, $fields, 'GET', sprintf('%s%s',$this->url,$endPoint), $secret);

        return $result;
    }

    /**
     * A new author for the account
     *
     * @param $data
     * @param $fields
     *
     * @return array
     */
    public  function createAuthorForElucidatAccount($account,$fields)
    {
        $secret = $this->clientSecret;
        $endPoint = 'authors/create';
        $key = (isset($account['elucidat_public_key']) && $account['elucidat_public_key']) ?$account['elucidat_public_key'] : $this->clientId;
        $data = \Savve\Stdlib\ObjectUtils::extract($fields);
        $nonce = $this->newAccessToken(sprintf('%s%s',$this->url,$endPoint), $key, $secret);
        $headers = $this->auth_headers($key, $nonce);
        $result = $this->_call($headers, $data, 'POST',sprintf('%s%s',$this->url,$endPoint), $secret);
        return $result;
    }


    /**
     * @param $author
     *
     * @return array
     */
    public  function deleteAuthorForElucidatAccount($author)
    {
        $secret = $this->clientSecret;
        $endPoint = 'authors/delete';
        $key = (isset($account['elucidat_public_key']) && $account['elucidat_public_key']) ?$account['elucidat_public_key'] : $this->clientId;
        $data = ['email' => $author['elucidatEmail']];

        $nonce = $this->newAccessToken(sprintf('%s%s',$this->url,$endPoint), $key, $secret);
        $headers = $this->auth_headers($key, $nonce);
        $result = $this->_call($headers, $data, 'POST',sprintf('%s%s',$this->url,$endPoint), $secret);
        return $result;
    }
    /**
     * Get Method for Token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
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
        $this->token = $token;
        return $this;
    }

    /**
     * Get Method for ClientId
     *
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
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
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Get Method for ClientSecret
     *
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
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
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * Get Method for GrantType
     *
     * @return mixed
     */
    public function getGrantType()
    {
        return $this->grantType;
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
        $this->grantType = $grantType;
        return $this;
    }

    /**
     * Get Method for Platform
     *
     * @return mixed
     */
    public function getPlatform()
    {
        return $this->platform;
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
        $this->platform = $platform;
        return $this;
    }

    /**
     * Get Method for Username
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
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
        $this->username = $username;
        return $this;
    }

    /**
     * Get Method for Password
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
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
        $this->password = $password;
        return $this;
    }

    /**
     * Get Method for Url
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
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
        $this->url = $url;
        return $this;
    }
    /**
     * Set Method for launch Url
     *
     * @param mixed $url
     *
     * @return Unirest
     */
    public function setBaseUrl ($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Get Method for ProjectUrl
     *
     * @return mixed
     */
    public function getProjectUrl()
    {
        return $this->projectUrl;
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
        $this->projectUrl = $projectUrl;
        return $this;
    }


    /**
     * Make a call to elucidat
     * @param $headers
     * @param $fields
     * @param $method
     * @param $url
     * @param $consumer_secret
     *
     * @throws Exception
     * @return array
     */
    private function _call($headers, $fields, $method, $url, $consumer_secret)
    {
        //Build a signature
        $headers['oauth_signature'] = $this->build_signature($consumer_secret, array_merge($headers, $fields), $method, $url);
        //Build OAuth headers
        $auth_headers = 'Authorization:';
        $auth_headers .= $this->build_base_string($headers, ',');
        //Build the request string
        $fields_string = $this->build_base_string($fields, '&');
        //Set the headers
        $header = array($auth_headers, 'Expect:');
        // Create curl options
        if(strcasecmp($method, self::METHOD_GET) == 0){
            $url .= '?'.$fields_string;
            $options = array(
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_HEADER => false,
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false);

        } else {
            $options = array(
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_HEADER => false,
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST => count($fields),
                CURLOPT_POSTFIELDS => $fields_string);
        }
        //Init the request and set its params
        $request = curl_init();
        curl_setopt_array($request, $options);
        //Make the request
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        curl_close($request);

        return array(
            'status' => $status,
            'response' => json_decode($response, true)
        );
    }


    /**
     * Computes and returns a signature for the request.
     *
     * @param $secret
     * @param $fields
     * @param $request_type
     * @param $url
     *
     * @return string
     */
    private function build_signature($secret, $fields, $request_type, $url)
    {
        ksort($fields);
        //Build base string to be used as a signature
        $base_info = $request_type . '&' . $url . '&'. $this->build_base_string($fields, '&');
        //return complete base string
        //Create the signature from the secret and base string
        $composite_key = rawurlencode($secret);
        return base64_encode(
            hash_hmac('sha1', $base_info, $composite_key, true)
        );

    }

    /**
     * Returns typical headers needed for a request
     *
     * @param $consumer_key
     * @param $nonce
     *
     * @return array
     */
    private function auth_headers($consumer_key, $nonce = '')
    {
        return array('oauth_consumer_key' => $consumer_key,
                     'oauth_nonce' => $nonce,
                     'oauth_signature_method' => 'HMAC-SHA1',
                     'oauth_timestamp' => time(),
                     'oauth_version' => '1.0');
    }


    /**
     * Builds a segment from an array of fields.  Its used to create string representations of headers and URIs
     *
     * @param $fields
     * @param $delim
     *
     * @return string
     */
    private function build_base_string($fields, $delim)
    {
        $r = array();
        foreach ($fields as $key => $value) {
            $r[] = rawurlencode($key) . "=" . rawurlencode($value);
        }
        return implode($delim, $r); //return complete base string
    }
}