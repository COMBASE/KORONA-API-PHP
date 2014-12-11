<?php
require_once("KoronaApi.class.php");

$appId = "your-app-id";
$secret = "your-app-secret";

$apiKey = "account api key";

try {
	$token = KoronaApi::auth($appId, $secret, $apiKey);

	echo "store this token: $token\r\n";
}
catch (Exception $e)
{
	echo "error";
}

?>

