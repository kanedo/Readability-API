<?php
/**
 * A API Wrapper for the Readability API
 * @see http://www.readability.com/publishers/api
 * @author Gabriel Bretschner <info@kanedo.net>
 * @package Kanedo_Readability
 **/
 
 require('OAuth.php');
 
 /**
  * Kanedo_Readability_Exception
  **/
  class Kanedo_Readability_Exception extends Exception {}
/**
 * The Wrapper Class Kanedo_Readability
 * This class encapsulate all important API features
 * @uses OAuth
 * @package Kanedo_Readability
 **/
class Kanedo_Readability {
	public $error = "";
	public $errnr = -1;

	private $c_key;
	private $c_secret;
	private $oauth_consumer;
	
	private $access_token = NULL;
	
	private $e_authorize = "https://www.readability.com/api/rest/v1/oauth/authorize/";
	private $e_request = "https://www.readability.com/api/rest/v1/oauth/request_token/";
	private $e_access = "https://www.readability.com/api/rest/v1/oauth/access_token/";
	
	private $api_base = "https://www.readability.com/api/rest/v1/";
	
	
	public function __construct($consumer_key, $consumer_secret) {
		$this->c_key = $consumer_key;
		$this->c_secret = $consumer_secret;
		$this->oauth_consumer = new OAuthConsumer($consumer_key, $consumer_secret, NULL);
	}
	
	protected function parseUrlQuery($query) {
		$parsed_result = array();
		$tmp = explode("&",$query);
		foreach($tmp as $part){
			$tmp2 = explode("=", $part);
			if(count($tmp2) == 2){
				$parsed_result[$tmp2[0]] = $tmp2[1];	
			}
		}
		return $parsed_result;
	}
	
	protected function makeHTTPRequest($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		$status = curl_getinfo($ch);
		curl_close($ch); 
		if($status['http_code'] != 200){
			throw new Kanedo_Readability_Exception("Bad Request({$url}): {$result}", $status['http_code']);
		}
		return $result;
	}
	
	public function makeAPIRequest($url, array $params = NULL, OAuthToken $aToken = NULL){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		
		$req = OAuthRequest::from_consumer_and_token($this->oauth_consumer, $token, "GET",$url, $params);
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, $token);
		$result = $this->makeHTTPRequest($req->to_url());
		return $result;
	}
	
	/**
	 * Generates the authorize URL
	 * @param $callback string The callback URL
	 * @return string The authorization URL
	 **/
	public function authorize($callback){
		$req = OAuthRequest::from_consumer_and_token($this->oauth_consumer, NULL, "GET",$this->e_authorize, array("oauth_callback" => $callback));
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, NULL);
		return $req->to_url();
	}
	
	public function request_token($oauth_verifier, $oauth_token, $oauth_callback_confirmed) {
		$req = OAuthRequest::from_consumer_and_token($this->oauth_consumer, NULL, "GET",$this->e_request, array(
											'oauth_verifier' => $oauth_verifier,
											'oauth_token' => $oauth_token,
											'oauth_callback_confirmed' => $oauth_callback_confirmed,
												));
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, NULL);
		$result = $this->makeHTTPRequest($req->to_url());
		return ($this->parseUrlQuery($result));
	}
	
	/**
	 * Requests an access token 
	 * @param array $results The results of Kanedo_Readability::request_token
	 * @return OAuthToken | false
	 **/
	public function access_token(array $results) {
		if(!array_key_exists("oauth_token", $results) ||
		   !array_key_exists("oauth_token_secret", $results) ||
		   !array_key_exists("oauth_verifier", $results) )
		   {
		   	throw new Kanedo_Readability_Exception('wrong parameter');
		   }
		   
		$token = new OAuthToken($results['oauth_token'], $results['oauth_token_secret']);
		$req = OAuthRequest::from_consumer_and_token(
					$this->oauth_consumer, 
					$token, 
					'GET',
					$this->e_access, 
					array('oauth_verifier' => $results['oauth_verifier'])
				);
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, $token);
		try{
			$result = $this->makeHTTPRequest($req->to_url());
		}catch (Kanedo_Readability_Exception $e) {
			$this->error = $e->getMessage();
			$this->errnr = $e->getCode();
			return false;
		}
		$token_credencials = ($this->parseUrlQuery($result));
		return $this->access_token = new OAuthToken($token_credencials['oauth_token'], $token_credencials['oauth_token_secret']);
	}
	
	public function getCurrentUser(OAuthToken $aToken = NULL) {
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		$url = $this->api_base."users/_current";
		var_dump($this->makeAPIRequest($url, NULL, $token));
	}
	
	public function getBookmarks(OAuthToken $aToken = NULL){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		$url = $this->api_base."bookmarks";
		var_dump($this->makeAPIRequest($url, NULL, $token));
	}
	
	/**
	 * retrieves every favorite bookmark between an optional given time frame
	 * @param $start timestamp time since favorited
	 * @param $end timestamp time until favorited
	 * @param $aToken OAuthToken access token
	 * @return array List of Bookmark objects
	 **/
	public function getFavorites($start=NULL, $end=NULL, OAuthToken $aToken = NULL){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		$url = $this->api_base."bookmarks";
		
		$fend= date("Y-m-d", $end);
		$params = array(
					'favorite' => 1,
					);
		if($start != NULL){
			$params['favorited_since'] = date("Y-m-d", $start);
		}
		
		if($end != NULL){
			$params['favorited_until'] = date("Y-m-d", $end);
		}
		$results = $this->makeAPIRequest($url, $params, $token);
		$json = json_decode($results);
		if($json == NULL){
			throw new Kanedo_Readability_Exception("bad json");
		}
		return $json->bookmarks;
	}
}
?>