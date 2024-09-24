<?php

namespace App\Core;

use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Core\Components\Result;
use App\Core\Components\Router;
use App\Core\Components\Middleware;
use App\Enum\StatusCode;
use App\Exception\Exception;
use App\Exception\HttpException;
use App\Exception\NotFoundException;

class App {

  /**
   * @var static
   */
  private static $instance = null;

  /**
   * @return static
   */
  static function getInstance() {
    return self::$instance;
  }

  static function createAppInstance() {
    self::$instance = new static();
  }

  private $path = '';
  private $method = '';

  /**
   * @var Router
   */
  private $router = null;
  private $routerRequested = null;
  /**
   * @var Request
   */
  private $request = null;
  /**
   * @var Response
   */
  private $response = null;

  private function __construct() {
  }

  static function CreateApp() {
    $path = '/';

    if (!isset($_GET['url']))
      $_GET['url'] = $_SERVER['REQUEST_URI'];

    isset($_GET['url']) && $path .= $_GET['url'];

    $path = str_replace('//', '/', $path);

    $method = $_SERVER['REQUEST_METHOD'] ?? '';

    self::makeApp($path, $method);

    return App::getInstance();
  }

  private static function makeApp(string $path, string $method) {
    self::createAppInstance();
    self::$instance->initialComponents($path, $method);
  }

  private function initialComponents(string $path, string $method) {
    try {
      $this->path = $path;
      $this->method = $method;

      $this->router = Router::getInstance();

      $this->fetchRouterRequested();

      $params = Router::getParamsFromRouter($this->routerRequested['router'], $this->path);

      $this->request = Request::createRequestInstance($path, $method, $params);
      $this->response = Response::getInstance();
    } catch (Exception $err) {
      $status = 400;

      if ($err instanceof HttpException) {
        $status = $err->getStatusCode();
      }

      Response::getInstance()
        ->sendJson(Result::failure($err->getInfoError(), $status));
    } catch (\Exception $err) {
      Response::getInstance()
        ->sendJson(Result::failure(['message' => $err->getMessage()], StatusCode::INTERNAL_SERVER_ERROR->value));
    }
  }

  private function fetchRouterRequested() {
    $router = $this->router->getRouteRequested($this->method, $this->path);

    if (!$router)
      throw new NotFoundException("Router \"$this->method\" \"$this->path\" not found");

    $this->routerRequested = $router;
  }

  function Run() {
    try {
      $this->resolveHandlers($this->routerRequested['handlers']);
    } catch (Exception $err) {
      $status = 400;

      if ($err instanceof HttpException) {
        $status = $err->getStatusCode();
      }

      Response::getInstance()
        ->sendJson(Result::failure($err->getInfoError(), $status));
    } catch (\Exception $err) {
      Response::getInstance()
        ->sendJson(Result::failure(['message' => $err->getMessage()], StatusCode::INTERNAL_SERVER_ERROR->value));
    }
  }

  private function resolveHandlers(array $handlers) {
    if (!$handlers)
      return Response::getInstance()
        ->sendJson(Result::failure(['message' => 'Method not implemented'], StatusCode::NOT_IMPLEMENTED->value));

    foreach ($handlers as $handler) {
      $controller = $handler[0] ?? null;
      $methodAction = $handler[1] ?? null;

      $response = $this->resolveCallHandler($controller, $methodAction);
      $this->resolveResponseHandler($response);
    }

    if ($this->response->getDataResponse() === null)
      $this->response->setDataResponse(Result::success(null));

    $this->response->sendDataResponse(StatusCode::OK->value);
  }

  private function resolveCallHandler($controller, ?string $methodAction = null) {
    if (!$controller)
      return;

    if (!is_string($controller) || !class_exists($controller)) {
      if (!is_callable($controller))
        return;

      return $controller($this->request, $this->response);
    }

    $controllerInstance = new $controller;

    if ($controllerInstance instanceof Middleware)
      $methodAction = 'perform';
    else if (empty($methodAction) || !method_exists($controllerInstance, $methodAction))
      return;

    return $controllerInstance->$methodAction($this->request, $this->response);
  }

  private function resolveResponseHandler($response) {
    if ($response === null)
      return;

    if (!$response instanceof Result)
      $response = Result::success($response);

    if (!$response->isSuccess())
      $this->response->sendJson($response);

    $this->response->setDataResponse($response);
  }
}
