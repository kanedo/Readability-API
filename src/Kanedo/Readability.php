<?php
/**
 * A API Wrapper for the Readability API TEST
 * @see http://www.readability.com/publishers/api
 * @author Gabriel Bretschner <info@kanedo.net>
 * @package Kanedo_Readability
 **/
 namespace Kanedo;
 require('OAuth.php');
 require('curl.php');
 
 /**
  * Kanedo_Readability_Exception
  **/
  class Kanedo_Readability_Exception extends Exception {
  	  protected $body;
	  protected $headers; 
	  public function __construct($msq = "", $code = 0){
		  $msq = $msq;
		  if($msq instanceof CurlResponse){
		  	  $code = $msq->headers['Status-Code'];
		  	  $pre = "Error: ";
		  	  switch($code){
			  	  case 401:
			  	  	$pre = "Authorization Required";
			  	  	break;
			  	  case 404:
			  	  	$pre = "Not Found";
			  	  	break;
			  	  case 500:
			  	  	$pre = "Internal Server Error";
			  	  	break;
			  	  case 400:
			  	  	$pre = "Bad Request";
			  	  	break;
			  	  case 409:
			  	  	$pre = "Conflict";
			  	  	break;
			  	  case 403:
			  	  	$pre = "Forbidden";
			  	  	break;
		  	  }
			  $this->body = json_decode($msq->body);
			  $this->headers = $msq->headers;
			  $msg = $pre.": ".print_r($this->body ,true)." HEADERS: ".print_r($this->headers, true);
		  }
		  
		  parent::__construct($msg, $code);
	  }
	  
	  public function getBody(){
		  return $this->body;
	  }
	  
	  public function getHeaders(){
		  return $this->headers;
	  }
  }
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
	
	/** 
	 * This method makes an HTTP Request with help of the curl wrapper
	 * It throws an exception if the status code is not 200 or 202
	 * @see https://github.com/shuber/curl
	 * @author Gabriel Bretschner
	 * @package Readability API
	 * @since 4301344192
	 **/
	protected function makeHTTPRequest($url, $param = array(), $method="GET"){
		$curl = new Curl();
		switch($method){
			case "GET":
				$result = $curl->get($url, $param);
				break;
			case "POST":
				$result = $curl->post($url, $param);
				break;
		}
		if(!($result->headers['Status-Code'] == 202 || $result->headers['Status-Code'] == 200)){
			throw new Kanedo_Readability_Exception($result);
		}
		return $result;
	}
	/** 
	 * Make an API Request
	 * This method takes an url with parameters and signs it.
	 * It makes an HTTP Request and returns the result without interpreting it.
	 *
	 * @author Gabriel Bretschner
	 * @package Readability API
	 * @since 4301344192
	 **/
	public function makeAPIRequest($url, array $params = NULL, OAuthToken $aToken = NULL, $method="GET"){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		
		$req = OAuthRequest::from_consumer_and_token($this->oauth_consumer, $token, $method, $url, $params);
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, $token);
		$result = $this->makeHTTPRequest($req->to_url(),$params, $method);
		return $result;
	}
	
	/**
	 * Generates the authorize URL
	 * @param $callback string The callback URL
	 * @return string The authorization URL
	 **/
	public function authorize($callback){
		$req = OAuthRequest::from_consumer_and_token(
			$this->oauth_consumer, 
			NULL, 
			"GET",
			$this->e_authorize, 
			array(
				"oauth_callback" => $callback
			)
		);
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, NULL);
		return $req->to_url();
	}
	
	public function request_token($oauth_verifier, $oauth_token, $oauth_callback_confirmed) {
		$req = OAuthRequest::from_consumer_and_token(
			$this->oauth_consumer, 
			NULL, 
			"GET",
			$this->e_request, 
			array(
				'oauth_verifier' => $oauth_verifier,
				'oauth_token' => $oauth_token,
				'oauth_callback_confirmed' => $oauth_callback_confirmed,
			)
		);
		$req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->oauth_consumer, NULL);
		$result = $this->makeHTTPRequest($req->to_url(), 'GET');
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
	
	/** 
	 * Retrieves information about the current logged in user
	 * @author Gabriel Bretschner
	 * @package Readability API
	 * @since 4301344192
	 **/
	public function getCurrentUser(OAuthToken $aToken = NULL) {
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		$url = $this->api_base."users/_current";
		$result = $this->makeAPIRequest($url, NULL, $token);
		$result = json_decode($result->body);
		if($result == NULL){
			throw new Kanedo_Readability_Exception("bad json");
		}
		
		return $result;
	}
	
	/** 
	 * Retrieves all Bookmarks available
	 *
	 * @param OAuthToken $aToken the optional token
	 * @author Gabriel Bretschner
	 * @package Readability API
	 * @since 4301344192
	 **/
	public function getBookmarks(OAuthToken $aToken = NULL){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;
		$url = $this->api_base."bookmarks";
		$result = $this->makeAPIRequest($url, NULL, $token);
		return json_decode($result->body);
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
	
	/** 
	 * Adds a bookmark to reading list
	 * @author Gabriel Bretschner
	 * @package Readability API
	 * @since 4301344192
	 **/
	
	public function addBookmark($url, $fav=0, $archive = 0, OAuthToken $aToken = NULL){
		if($aToken == NULL && $this->access_token == NULL){
			throw new Kanedo_Readability_Exception('access token required');
		}
		$token = ($aToken == NULL)?$this->access_token:$aToken;

		$aurl = $this->api_base."bookmarks";

		$params = array(
					'url' => $url,
					'favorite' => $fav,
					'archive' => $archive,
				); /**/

		try{
			$result = $this->makeAPIRequest($aurl, $params, $token, 'POST');
			if($result->headers['Status-Code'] == 202){
				return true;
			}
		}catch(Kanedo_Readability_Exception $e){
			if($e->getCode() != 409){
				error_log($e->getMessage());
				return false;
			}
			$result = $e->getMessage(); 
		} 
		return $result;
	}
}


?>