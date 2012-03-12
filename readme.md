#Readability API
This is an API wrapper of the [Readability API](http://www.readability.com/publishers/api/) to use in PHP.

##external Libraries
This wrapper uses the [OAuth Library](http://code.google.com/p/oauth-php/) which is under the MIT License.

##Requirements
###Readability API Key
You need an API Key from Readability. You can Request one at their [contact page](http://www.readability.com/contact)
###cURL
In order to make HTTP-Request this Lib uses the cURL PHP Extension. Other wrapper are planned.

##Usage
Initiate the object with your token and secret:

	$api = new Kanedo_Readability("consumer_token", "consumer_secret");

To use the lib you first have to **authorize** yourself with readability. In order to do this you have to make three requests.

1. The authorization (The user give permission)
You have to redirect to the authorization page and provide a callback URL.
	
		$api->authorize("http://example.org/callback");

2. Request a token

		$result = $api->request_token($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'], $_REQUEST['oauth_callback_confirmed']);  

	Readability will send three parameters when redirecting back to your callback url - we need this params

3. Access token
	
		$token = $api->access_token($result);  

	This method returns an OAuthToken object. You can either store it or, if you only use this single instance, you can just forget it ;-)

4. Request some information  
In order to do that I've implemented an method called `makeAPIRequest($url, array $params = NULL, OAuthToken $aToken = NULL)` The first param is the URL (to access the base url you can use the property `api_base`) the second is an array of parameters - it can be `NULL`. The last parameter is the OAuthToken. If you use a single instance (in which case the token is stored inside the object) you can leave this blank, otherwise you to provide the object which was returned by `access_token()`.   
The method `makeAPIRequest` returns unparsed JSON - you can do what ever you want to.  
It's planned to encapsulate everything in special methods like `getFavorites`.

##Feedback
If you want to get in touch with me, visit my blog at [http://blog.kanedo.net](http://blog.kanedo.net). I would like to hear from you. Especially if you find a bug or something like that!