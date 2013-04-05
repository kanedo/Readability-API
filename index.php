<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'Kanedo_Readability.php';

$token = new OAuthToken("kQxQnydJDRTktewyDU", "hqhThVE2KBnG82qBLCsabNdjv4hrEZy6");
$api = new Kanedo_Readability("Kanedo", "cFbkAVP22g2uLpV74cbZCXmjCyyD3Lhb");
echo "<a href='{$api->authorize("http://readability-api.dev")}'>authorize</a>";
$result = $api->request_token($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'], $_REQUEST['oauth_callback_confirmed']); 
$token = $api->access_token($result);

echo "<pre>";
var_dump($api->addBookmark("http://blog.kanedo.net/1357,links-vom-27-marz-2013-bis-29-marz-2013.html", 0));
//var_dump($api->getFavorites());
echo "</pre>";