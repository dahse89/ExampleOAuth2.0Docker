<?php
require_once 'vendor/autoload.php';


const CLIENT_ID     = 'testclient';
const CLIENT_SECRET = 'testpass';

const REDIRECT_URI           = 'http://localhost/oauth2/php_oauth_client/www/index.php';
const AUTHORIZATION_ENDPOINT = 'http://localhost:81/oauth/authorize';
const TOKEN_ENDPOINT         = 'http://localhost:81/oauth/token';

$client = new OAuth2\Client(CLIENT_ID, CLIENT_SECRET);

$get = (object) filter_input_array(INPUT_GET,[
    'code' => FILTER_SANITIZE_STRING,
    'error' => FILTER_SANITIZE_STRING,
    'error_description' => FILTER_SANITIZE_STRING,
    'get_access' => FILTER_VALIDATE_BOOLEAN
]);


if(@$get->error){
    echo "<a href='$_SERVER[PHP_SELF]'>retry</a><br>";
    die("<b>{$get->error}</b><br>{$get->error_description}");
}

if (!@$get->code && @$get->get_access){
    $auth_url = $client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI, ['state' => uniqid()]);
    header('Location: ' . $auth_url);
    die('Redirect');
}

if(@$get->code){
    $params = array('code' => $get->code, 'redirect_uri' => REDIRECT_URI);
    $response = $client->getAccessToken(TOKEN_ENDPOINT, 'authorization_code', $params);
    echo "<b>Access token:</b> ".$response['result']['access_token'].'<br>';
    echo "<a href='$_SERVER[PHP_SELF]'>back</a><br>";
}else{

}

echo "<a href='$_SERVER[PHP_SELF]?get_access=true'>get access</a><br>";


