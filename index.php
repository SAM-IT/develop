<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$dir = __DIR__;
while (!file_exists($dir . '/vendor/autoload.php') && $dir != dirname($dir)) {
    $dir = dirname($dir);
}
if (!file_exists($dir . '/vendor/autoload.php')) {
    die("Could not find autoload.php");
}
require $dir . '/vendor/autoload.php';

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
