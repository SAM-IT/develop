<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists('/home/vagrant/.composer/vendor/autoload.php')) {
    require '/home/vagrant/.composer/vendor/autoload.php';
} else {
    die("Could not find autoload.php");
}


$app = new \Slim\App;
$app->get('/', function (Request $request, Response $response) {
    ob_start();
    require 'view.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->get('/logs', function(Request $request, Response $response) {
    ob_start();
    require __DIR__ . '/vendor/potsky/pimp-my-log/index.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});
$app->run();
