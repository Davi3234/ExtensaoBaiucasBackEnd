<?php

namespace Core\HTTP;

class Router {

  static function isMathPrefixRouterTemplate(string $router, string $routerTemplate) {
    $pattern = static::getPatternRouterMatching($routerTemplate);

    return preg_match('/^' . $pattern . '/', $router);
  }

  static function isMathRouterTemplate(string $router, string $routerTemplate) {
    $pattern = static::getPatternRouterMatching($routerTemplate);

    return preg_match('/^' . $pattern . '$/', $router);
  }

  static function getParamsFromRouter(string $router, string $routerTemplate) {
    preg_match_all('/:([a-zA-Z]+)/', $routerTemplate, $params);
    $params = $params[1];

    $pattern = static::getPatternRouterMatching($routerTemplate);

    if (preg_match('/^' . $pattern . '$/', $router, $matches)) {
      array_shift($matches);
      return array_combine($params, $matches);
    }

    return [];
  }

  static function getPatternRouterMatching(string $routerTemplate) {
    return preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $routerTemplate));
  }
}