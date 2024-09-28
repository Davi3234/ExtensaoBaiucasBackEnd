<?php

namespace App\Core\Components;

use App\Enum\RouterMethod;
use App\Exception\CriticalException;

class Router {

  private static $instance = null;

  /**
   * @return static
   */
  static function getInstance() {
    if (!static::$instance)
      static::$instance = new static();

    return static::$instance;
  }

  /**
   * @var array<string, array{prefix: string, filePath: string}>
   */
  protected $routersGroup = [];

  /**
   * @var array{
   * GET: array<string, array{router: string, handlers: array}>,
   * POST: array<string, array{router: string, handlers: array}>,
   * PUT: array<string, array{router: string, handlers: array}>,
   * DELETE: array<string, array{router: string, handlers: array}>,
   * PATCH: array<string, array{router: string, handlers: array}>,
   * HEAD: array<string, array{router: string, handlers: array}>,
   * OPTIONS: array<string, array{router: string, handlers: array}>,
   * }
   */
  protected $routers = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => [],
    'PATCH' => [],
    'HEAD' => [],
    'OPTIONS' => [],
  ];

  /**
   * @param array{prefix: string, filePath: string}[] ...$args
   * @return void
   */
  function addRouterGroup(array ...$args) {
    foreach ($args as $arg) {
      if (isset($this->routersGroup[$arg['prefix']]))
        throw new CriticalException("Prefix router group \"{$arg['prefix']}\" already defined");

      $this->routersGroup[$arg['prefix']] = $arg;
    }
  }

  protected function createRouter(string $method, string $path, $handlers) {
    $path = str_replace('//', '/', trim("/$path"));

    if (!$path || $path == '/')
      $path = '';

    foreach ($handlers as &$handler) {
      if (!is_array($handler))
        $handler = [$handler];
    }

    if (isset($this->routers[$method][$path]))
      throw new CriticalException("Router \"$method\" \"$path\" already defined");

    $this->routers[$method][$path] = [
      'router' => $path,
      'handlers' => $handlers,
    ];
  }

  function getRouteRequested(string $method, string $routerRequest) {
    $routersGroup = $this->getRoutersGroupByRouter($routerRequest);

    foreach ($routersGroup as $routerGroup)
      @include str_replace('\\', '/', __DIR__ . '/../../' . $routerGroup['filePath']);

    $router = $this->getRouterByMethodAndRouter($method, $routerRequest);

    return $router;
  }

  function getRoutersGroupByRouter(string $router) {
    $routersGroup = [];

    foreach ($this->routersGroup as $prefix => $routerGroup)
      if (static::isMathPrefixRouterTemplate($prefix, $router))
        $routersGroup[] = $routerGroup;

    return $routersGroup;
  }

  function getRoutersGroup() {
    return $this->routersGroup;
  }

  /**
   * @return string[]
   */
  function getAllRoutersPaths(string $method) {
    return array_keys($this->routers[$method]);
  }

  function getRouters() {
    return $this->routers;
  }

  function getRouterByMethodAndRouter(string $method, string $routerPath) {
    foreach ($this->routers[$method] as $prefix => $router) {
      if (static::isMathRouterTemplate($prefix, $routerPath)) {
        return $router;
      }
    }

    return null;
  }

  function getRoutersByPrefix(string $method, string $prefix) {
    return $this->routers[$method][$prefix] ?: null;
  }

  static function isMathPrefixRouterTemplate(string $routerTemplate, string $router) {
    $pattern = static::getPatternRouterMatching($routerTemplate);

    return preg_match('/^' . $pattern . '/', $router);
  }

  static function isMathRouterTemplate(string $routerTemplate, string $router) {
    $pattern = static::getPatternRouterMatching($routerTemplate);

    return preg_match('/^' . $pattern . '$/', $router);
  }

  static function getParamsFromRouter(string $routerTemplate, string $router) {
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

  static function writeRouter(array ...$args) {
    static::getInstance()->addRouterGroup(...$args);
  }

  static function get(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::GET->value, $path, $handlers);
  }

  static function post(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::POST->value, $path, $handlers);
  }

  static function put(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::PUT->value, $path, $handlers);
  }

  static function patch(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::PATCH->value, $path, $handlers);
  }

  static function delete(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::DELETE->value, $path, $handlers);
  }

  static function head(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::HEAD->value, $path, $handlers);
  }

  static function options(string $path, ...$handlers) {
    static::getInstance()->createRouter(RouterMethod::OPTIONS->value, $path, $handlers);
  }

  static function maker(string $prefix = '') {
    return new RouterMake($prefix);
  }
}

class RouterMake {
  private $prefix = '';

  function __construct($prefix = '') {
    $this->prefix = $prefix;
  }

  function get(string $path, ...$handlers) {
    Router::get($this->createPath($path), ...$handlers);
  }

  function post(string $path, ...$handlers) {
    Router::post($this->createPath($path), ...$handlers);
  }

  function put(string $path, ...$handlers) {
    Router::put($this->createPath($path), ...$handlers);
  }

  function delete(string $path, ...$handlers) {
    Router::delete($this->createPath($path), ...$handlers);
  }

  function patch(string $path, ...$handlers) {
    Router::patch($this->createPath($path), $handlers);
  }


  function head(string $path, ...$handlers) {
    Router::head($this->createPath($path), $handlers);
  }

  function options(string $path, ...$handlers) {
    Router::options($this->createPath($path), $handlers);
  }

  protected function createPath(string $path) {
    if (!$path || $path == '/')
      $path = '';

    return $this->prefix . $path;
  }
}
