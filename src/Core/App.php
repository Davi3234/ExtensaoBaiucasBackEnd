<?php

namespace App\Core;

use App\Exception\Exception;
use App\Exception\CriticalException;
use App\Exception\Http\RouterNotFoundException;
use App\Core\Components\Request;
use App\Core\Components\Response;
use App\Core\Components\Result;
use App\Core\Components\Router;
use App\Core\Components\Middleware;
use App\Enum\StatusCode;

class App {

  private static $instance;

  static function getInstance(): static {
    return self::$instance;
  }

  static function createAppInstance() {
    self::$instance = new static();
  }

  private $path = '';
  private $method = '';

  private Router $router;
  private array $routerRequested;
  private Request $request;
  private Response $response;

  private function __construct() {
  }

  static function CreateApp() {
    if (!isset($_GET['url']))
      $_GET['url'] = $_SERVER['REQUEST_URI'];

    if (!$_GET['url'])
      $_GET['url'] = '/';

    $_GET['url'] = str_replace('//', '/', $_GET['url']);

    $method = $_SERVER['REQUEST_METHOD'] ?? '';

    self::makeApp($_GET['url'], $method);

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
    } catch (\Exception $err) {
      Response::getInstance()->sendJson(self::resolveErrorToResult($err));
    }
  }

  private function fetchRouterRequested() {
    $router = $this->router->getRouteRequested($this->method, $this->path);

    if (!$router)
      throw new RouterNotFoundException("Router \"$this->method\" \"$this->path\" not found");

    $this->routerRequested = $router;
  }

  function Run() {
    try {
      $this->resolveHandlers($this->routerRequested['handlers']);
    } catch (\Exception $err) {
      Response::getInstance()->sendJson(self::resolveErrorToResult($err));
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

  static function resolveErrorToResult(\Exception $err) {
    if ($err instanceof CriticalException)
      return Result::failure(self::resolveCriticalErrorMessage($err->getInfoError()), StatusCode::INTERNAL_SERVER_ERROR->value);

    if ($err instanceof Exception)
      return $err->toResult();

    return Result::failure(self::resolveCriticalErrorMessage(['message' => $err->getMessage()]), StatusCode::INTERNAL_SERVER_ERROR->value);
  }

  private static function resolveCriticalErrorMessage($message) {
    if (env('ENV') == 'PROD')
      return ['message' => 'Internal server error. Please try again later'];

    return $message;
  }
}
