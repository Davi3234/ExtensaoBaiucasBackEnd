<?php

namespace App\Core;

use App\Core\Components\Attribute\Controller;
use App\Core\Components\Attribute\Get;
use App\Core\Components\Attribute\Guard;
use App\Core\Components\Attribute\Post;
use App\Core\Components\Attribute\RouterMap;
use App\Core\Components\Request;
use App\Core\Components\RequestBuilder;
use App\Core\Components\Response;
use App\Core\Components\Router;
use App\Exception\Http\RouterNotFoundException;

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
      $this->loadParamsInRequest();
    } catch (\Exception $err) {
      echo $err->getMessage();
    }
  }

  private function loadParamsInRequest() {
    $fullPath = $this->requestManager->getFullPathRequested();

    $params = Router::getParamsFromRouter($fullPath, $this->pathHttp);

    $this->requestBuilder->setParams($params);
  }
}
