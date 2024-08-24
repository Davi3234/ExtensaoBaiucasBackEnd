<?php

namespace App\Core;

use App\Core\Components\Middleware;
use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Core\Components\Result;
use App\Core\Components\Router;
use App\Exception\HttpException;
use App\Exception\RouterNotFoundException;

class App {

  private static $instance = null;

  /**
   * @return App
   */
  static function getInstance() {
    if (!static::$instance)
      static::$instance = new static();

    return static::$instance;
  }

  protected $path = '';
  protected $method = '';

  /**
   * @var Router
   */
  protected $router = null;
  protected $routerRequested = null;

  private function __construct() {
    $this->router = Router::getInstance();
  }

  static function CreateApp() {
    $path = '/';

    isset($_GET['url']) && $path .= $_GET['url'];

    $path = str_replace('//', '/', $path);

    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

    static::makeApp($path, $method);

    return App::getInstance();
  }

  protected static function makeApp($path, $method) {
    App::getInstance();
    Request::getInstance();
    Response::getInstance();

    static::$instance->method = $method;
    static::$instance->path = $path;

    static::$instance->initialComponents();
  }

  protected function initialComponents() {
    try {
      $this->fetchRouterRequested();
    } catch (\Exception $err) {
      if ($err instanceof HttpException) {
        Response::getInstance()
          ->sendJson(Result::failure(['message' => $err->getMessage()], $err->getStatusCode()));
      }
    }
  }

  protected function fetchRouterRequested() {
    $router = $this->router->getRouteRequested($this->method, $this->path);

    if (!$router)
      throw new RouterNotFoundException("Router \"$this->method\" \"$this->path\" not found");

    $params = Router::getParamsFromRouter($router['router'], $this->path);

    foreach ($params as $param => $value) {
      Request::getInstance()->setParam($param, $value);
    }

    $this->routerRequested = $router;
  }

  function Run() {
    try {
      $this->resolveHandlers($this->routerRequested['handlers']);
    } catch (\Exception $err) {
      if ($err instanceof HttpException) {
        Response::getInstance()
          ->sendJson(Result::failure(['message' => $err->getMessage()], $err->getStatusCode()));
        return;
      }

      Response::getInstance()
        ->sendJson(Result::failure(['message' => $err->getMessage()], 500));
    }
  }

  protected function resolveHandlers($handlers) {
    foreach ($handlers as $handler) {
      $controller = isset($handler[0]) ? $handler[0] : null;
      $methodAction = isset($handler[1]) ? $handler[1] : null;

      $response = $this->resolveCallHandler($controller, $methodAction);
      $this->resolveResponseHandler($response);
    }

    Response::getInstance()
      ->sendJson(Result::success('No response', 204));
  }

  protected function resolveCallHandler($controller, $methodAction) {
    if (!$controller)
      return;

    if (!is_string($controller) || !class_exists($controller)) {
      if (!is_callable($controller))
        return;

      return $controller(Request::getInstance(), Response::getInstance());
    }

    $controllerInstance = new $controller;

    if ($controllerInstance instanceof Middleware)
      $methodAction = 'perform';
    else if (empty($methodAction) || !method_exists($controllerInstance, $methodAction))
      return;

    return $controllerInstance->$methodAction(Request::getInstance(), Response::getInstance());
  }

  protected function resolveResponseHandler($response) {
    if ($response === null)
      return;

    if ($response instanceof Result) {
      Response::getInstance()
        ->sendJson($response);
    }

    Response::getInstance()
      ->sendJson(Result::success($response, 200));
  }
}
