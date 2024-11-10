<?php

namespace Core;

use Core\Common\Result;
use Core\Enum\MethodHTTP;
use Core\Enum\StatusCodeHTTP;
use Core\Exception\CriticalException;
use Core\Exception\Exception;
use Core\HTTP\Request;
use Core\HTTP\RequestBuilder;
use Core\HTTP\Response;
use Core\HTTP\RouterURL;
use Core\Managers\RequestManager;

class Server {

  private RequestManager $requestManager;
  private RequestBuilder $requestBuilder;
  private Request $request;
  private Response $response;
  private string $routerHttp = '';
  private string $methodHttp = '';

  /** @var array{controller: string, method: string}[] */
  private array $handlers = [];

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  protected function __construct(private array $routersMap = []) {
    $this->routerHttp = Request::getRouterRequested();
    $this->methodHttp = Request::getMethodHttpRequested();

    $this->requestManager = new RequestManager($this->routersMap, $this->routerHttp, $this->methodHttp);
    $this->requestBuilder = new RequestBuilder();
    $this->response = new Response();

    $this->requestBuilder
      ->setPathHttp($this->routerHttp)
      ->setMethodHttp($this->methodHttp)
      ->setBody(Request::getBodyRequest())
      ->setParams(Request::getParamsRequest());
  }

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  static function Bootstrap(array $routersMap) {
    return new static($routersMap);
  }

  function dispatch() {
    try {
      if ($this->methodHttp != MethodHTTP::OPTIONS->value) {
        $this->handlers = $this->requestManager->loadRequest();
        $this->loadParamsRequest();
        $this->resolveHandlers();
      } else
        $this->response->setResponse(Result::success(true));
    } catch (\Exception $err) {
      $this->response->setResponse(static::resolveErrorToResult($err));
    }

    $this->sendResponse();
  }

  private function loadParamsRequest() {
    $fullPath = $this->requestManager->getEndpointRequested()['endpoint'];

    $params = RouterURL::getParamsFromRouter($fullPath, $this->routerHttp);

    $this->requestBuilder->setParams($params);
    $this->request = $this->requestBuilder->build();
  }

  private function resolveHandlers() {
    try {
      foreach ($this->handlers as $handler) {
        $response = $this->callHandler($handler['controller'], $handler['method']);

        $callbackResult = $this->resolveResponseHandler($response);

        if ($callbackResult['isEndRequest'])
          break;
      }
    } catch (\Exception $err) {
      $this->response->setResponse(static::resolveErrorToResult($err));
    }

    if ($this->response->getResponse() === null)
      $this->response->setResponse(Result::success(null));
  }

  /**
   * @param string $controller Controller name
   * @param string $methodName Method name
   * @return mixed Response of the controller
   */
  function callHandler(string $controller, string $methodName) {
    $controller = new $controller;

    $response = $controller->$methodName($this->request, $this->response);

    return $response;
  }

  private function resolveResponseHandler($response) {
    $isEndRequest = false;

    if ($response === null)
      return ['isEndRequest' => $isEndRequest];

    if (!$response instanceof Result)
      $response = Result::success($response);

    if (!$response->isSuccess())
      $isEndRequest = true;

    $this->response->setResponse($response);

    return ['isEndRequest' => $isEndRequest];
  }

  private function sendResponse() {
    $response = $this->response->getResponse();
    $statusCode = StatusCodeHTTP::OK->value;

    if ($response instanceof Result) {
      $statusCode = $response->getStatus();
    }

    $this->response->sendResponse($statusCode);
  }

  private static function resolveErrorToResult(\Exception $err) {
    if ($err instanceof CriticalException)
      return Result::failure(
        static::resolveCriticalErrorMessage($err->getInfoError()),
        StatusCodeHTTP::INTERNAL_SERVER_ERROR->value
      );

    if ($err instanceof Exception)
      return $err->toResult();

    return Result::failure(
      static::resolveCriticalErrorMessage(['message' => $err->getMessage()]),
      StatusCodeHTTP::INTERNAL_SERVER_ERROR->value
    );
  }

  private static function resolveCriticalErrorMessage($message) {
    if (env('ENV') == 'PROD')
      return ['message' => 'Internal server error. Please try again later'];

    return $message;
  }
}
