<?php

require 'initialize.php';

function isMathRouter(string $router, string $endpoint) {
  $pattern = getPatternPregRouter($endpoint);

  return preg_match('/^' . $pattern . '$/', $router);
}

function getPatternPregRouter(string $endpoint) {
  return preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $endpoint));
}

$routers = (array) json_decode(file_get_contents('./storage/app/routers.json'))->routers;

$method = 'POST';
$router = '/users/create';

$routersKeys = (array) $routers[$method];

$endpoint = array_find(function ($routerKey) use ($router) {
  return isMathRouter($router, ((array) $routerKey)['endpoint']);
}, $routersKeys);

var_dump($endpoint);
