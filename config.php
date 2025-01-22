<?php
require_once 'vendor/autoload.php';

// init configuration
$clientID = '667754488994-72mh4kcvnfqkh24bs7p4b472mi03d9pf.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost:8080/Projekt/Index.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Ensure Guzzle HTTP client is properly initialized
$httpClient = new \GuzzleHttp\Client();
$client->setHttpClient($httpClient);


?>