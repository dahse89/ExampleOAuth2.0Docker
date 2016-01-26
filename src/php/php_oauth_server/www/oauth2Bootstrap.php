<?php
$dsn      = 'mysql:host=db;dbname=test;port=3306';
$username = 'php';
$password = 'mysql';

$storagePdo = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
$storageRedis = new OAuth2\Storage\Redis(new \Predis\Client('tcp://redis:6379'));

$server = new OAuth2\Server([
    'client_credentials' => $storagePdo,
    'scope'              => $storagePdo,
    'access_token'       => $storageRedis,
    'authorization_code' => $storageRedis,
]);

$server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storageRedis));