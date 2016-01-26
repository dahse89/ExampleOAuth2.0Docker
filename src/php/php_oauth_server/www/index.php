<?php
require_once 'vendor/autoload.php';
require_once 'oauth2Bootstrap.php';

use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

$app = new \Slim\App(new \Slim\Container([
    'settings' => [
        'displayErrorDetails' => true,
    ],
    'errorHandler' => function ($c) {
        return function ($request, $response, $exception) use ($c) {
            return $c['response']->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write('Something went wrong!<br><pre>'.$exception->xdebug_message.'</pre>');
        };
    }
]));

// test route: does the server works?
$app->get('/oauth', function (Req $request, Res $response) {
    phpinfo();
    return $response;
});

$app->get('/redis-test',function(Req $request, Res $response){
    $client = new Predis\Client('tcp://redis:6379');
    $client->set('foo', 'bar');
    $value = $client->get('foo');
    $body = $response->getBody();
    $body->write($value);
});

$app->post('/oauth/token',function(Req $request, Res $response) use ($server){
    // Handle a request for an OAuth2.0 Access Token and send the response to the client
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    return $response;
});

$app->post('/oauth/resource',function(Req $request, Res $response) use ($server){
    // Handle a request to a resource and authenticate the access token
    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
        $server->getResponse()->send();
        die;
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $body = $response->getBody();
    $body->write(json_encode(array('success' => true, 'message' => 'You accessed my APIs!')));
    return $response;
});

$app->any('/oauth/authorize',function(Req $request, Res $response) use ($server){
    $oauthRequest = OAuth2\Request::createFromGlobals();
    $aouthResponse = new OAuth2\Response();

    // validate the authorize request
    if (!$server->validateAuthorizeRequest($oauthRequest, $aouthResponse)) {
        $aouthResponse->send();
        return $response;
    }

    if (empty($_POST)) {
        exit('<form method="post">
              <label>Do You Authorize TestClient?</label><br />
              <input type="submit" name="authorized" value="yes">
              <input type="submit" name="authorized" value="no">
            </form>');
    }

    // print the authorization code if the user has authorized your client
    $is_authorized = ($_POST['authorized'] === 'yes');
    $server->handleAuthorizeRequest($oauthRequest, $aouthResponse, true);
    if ($is_authorized) {
        $aouthResponse->send();
        die('Redirect');
    }

    $error_uri = $_GET['redirect_uri'].'?'.http_build_query([
            'error' => 'Access denied',
            'error_description' => 'user did not grand access'
        ]);
    return $response->withStatus(301)->withHeader('Location', $error_uri);
});

$app->run();