<?php

namespace App\Core;

use App\Core\Components\Request;
use App\Core\Components\RequestBuilder;
use App\Core\Components\Response;
use App\Core\Components\Result;
use App\Core\Components\Router;
use App\Enum\StatusCode;
use App\Exception\CriticalException;
use App\Exception\Exception;

class Server {

  private RequestManager $requestManager;
  private RequestBuilder $requestBuilder;
  private Request $request;
  private Response $response;
  private string $pathHttp = '';
  private string $methodHttp = '';

  /** @var array{controller: string, method: string}[] */
  private array $handlers = [];

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  protected function __construct(private array $routersMap = []) {
    $this->pathHttp = Request::getPathHttpRequested();
    $this->methodHttp = Request::getMethodHttpRequested();

    $this->requestManager = new RequestManager($this->routersMap, $this->pathHttp, $this->methodHttp);
    $this->requestBuilder = new RequestBuilder();
    $this->response = new Response();

    $this->requestBuilder
      ->setPathHttp($this->pathHttp)
      ->setMethodHttp($this->methodHttp)
      ->setBody(Request::getBodyRequest())
      ->setParams(Request::getParamsRequest());
  }

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  static function Fabric(array $routersMap) {
    return new static($routersMap);
  }

  function Run() {
    try {
      $this->handlers = $this->requestManager->loadRequest();
      $this->loadParamsRequest();
      $this->resolveHandlers();
    } catch (\Exception $err) {
      $this->response->sendJson(self::resolveErrorToResult($err));
    }
  }

  private function loadParamsRequest() {
    $fullPath = $this->requestManager->getFullPathRequested();

    $params = Router::getParamsFromRouter($fullPath, $this->pathHttp);

    $this->requestBuilder->setParams($params);
    $this->request = $this->requestBuilder->build();
  }

  private function resolveHandlers() {
    try {
      foreach ($this->handlers as $handler) {
        $controller = new $handler['controller'];
        $methodName = $handler['method'];

        $response = $controller->$methodName($this->request, $this->response);

        $this->resolveResponseHandler($response);
      }

      if ($this->response->getDataResponse() === null)
        $this->response->setDataResponse(Result::success(null));

      $this->response->sendDataResponse(StatusCode::OK->value);
    } catch (\Exception $err) {
      $this->response->sendJson(self::resolveErrorToResult($err));
    }
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

  private static function resolveErrorToResult(\Exception $err) {
    if ($err instanceof CriticalException)
      return Result::failure(
        self::resolveCriticalErrorMessage($err->getInfoError()),
        StatusCode::INTERNAL_SERVER_ERROR->value
      );

    if ($err instanceof Exception)
      return $err->toResult();

    return Result::failure(
      self::resolveCriticalErrorMessage(['message' => $err->getMessage()]),
      StatusCode::INTERNAL_SERVER_ERROR->value
    );
  }

  private static function resolveCriticalErrorMessage($message) {
    if (env('ENV') == 'PROD')
      return ['message' => 'Internal server error. Please try again later'];

    return $message;
  }
}
