<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'Kanedo_Readability.php';

$token = new OAuthToken("kQxQnydJDRTktewyDU", "hqhThVE2KBnG82qBLCsabNdjv4hrEZy6");
$api = new Kanedo_Readability("Kanedo", "cFbkAVP22g2uLpV74cbZCXmjCyyD3Lhb");
echo "<a href='{$api->authorize("http://readability.work")}'>authorize</a>";
$result = $api->request_token($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'], $_REQUEST['oauth_callback_confirmed']); 
$token = $api->access_token($result);

$api->addBookmark("http://blog.kanedo.net/1129,custom-post-type-mit-passwort-versehen.html", 0);
echo "<pre>";
var_dump($api->getCurrentUser());
echo "</pre>";