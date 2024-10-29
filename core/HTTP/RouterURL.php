<?php

namespace Core\HTTP;

class RouterURL {

  static function isMathPrefixRouter(string $router, string $endpoint) {
    $pattern = static::getPatternPregRouter($endpoint);

    return preg_match('/^' . $pattern . '/', $router);
  }

  static function isMathRouter(string $router, string $endpoint) {
    $pattern = static::getPatternPregRouter($endpoint);

    return preg_match('/^' . $pattern . '$/', $router);
  }

  static function getParamsFromRouter(string $router, string $endpoint) {
    $paramsName = static::getParamsNameFromEndpoint($endpoint);
    $pattern = static::getPatternPregRouter($endpoint);

    if (preg_match('/^' . $pattern . '$/', $router, $matches)) {
      array_shift($matches);
      return array_combine($paramsName, $matches);
    }

    return [];
  }

  static function getParamsNameFromEndpoint(string $endpoint) {
    preg_match_all('/:([a-zA-Z]+)/', $endpoint, $params);

    return $params[1];
  }

  static function getPatternPregRouter(string $endpoint) {
    return preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $endpoint));
  }
}
