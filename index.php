<?php
require 'Kanedo_Readability.php';

$api = new Kanedo_Readability("Kanedo", "cFbkAVP22g2uLpV74cbZCXmjCyyD3Lhb");
echo "<a href='{$api->authorize("http://readability.api/")}'>A</a>";

$result = $api->request_token($_REQUEST['oauth_verifier'], $_REQUEST['oauth_token'], $_REQUEST['oauth_callback_confirmed']);

var_dump($api->access_token($result));