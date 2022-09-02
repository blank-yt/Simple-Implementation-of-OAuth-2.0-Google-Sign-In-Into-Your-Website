<?php
session_start();
require_once("./login/vendor/autoload.php");

$client = new Google_Client();
$client->setAuthConfig(''); /* REPLACE WITH YOUR CREDENTIALS.json FILE NAME FROM GOOGLE */

unset($_SESSION['upload_token']);
$client->revokeToken();
session_destroy();

header("Location:index.php");