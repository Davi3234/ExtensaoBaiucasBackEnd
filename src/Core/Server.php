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

  private static $HTTP_METHODS_ATTRIBUTES = [Get::class, Post::class];

  private static $instance;
  private RequestBuilder $requestBuilder;
  private Request $request;
  private Response $response;
  private string $pathHttp = '';
  private string $methodHttp = '';

  /** @var array{controllers: class-string[]} $routers */
  private array $routers = [];

  protected function __construct() {
    $this->requestBuilder = new RequestBuilder();

    $this->pathHttp = Router::getPathHttpRequested();
    $this->methodHttp = Router::getMethodHttpRequested();

    $dataJson = file_get_contents('php://input');
    $data = json_decode($dataJson, true);

    $body = $data;
    $params = $_GET;

    $this->requestBuilder
      ->setPathHttp($this->pathHttp)
      ->setMethodHttp($this->methodHttp)
      ->setBody($body ?? [])
      ->setParams($params ?? []);

    $this->response = new Response();
  }

  static function getInstance(): static {
    if (static::$instance == null) {
      static::$instance = new static;
    }

    return static::$instance;
  }

  /**
   * @param array{controllers: class-string[]} $routers
   */
  static function Fabric(array $routers) {
    return static::getInstance()->bootstrap($routers);
  }

  /**
   * @param array{controllers: class-string[]} $routers
   */
  private function bootstrap(array $routers) {
    $this->routers = $routers;

    return $this;
  }

  function Run() {
    try {
      $controllerRequested = $this->fetchControllerRequestTarget();

      if (!$controllerRequested) {
        throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->pathHttp\" not found");
      }

      $methodRequested = $this->fetchMethodRequestTargetFromController($controllerRequested['reflectionClass'], $controllerRequested['controllerAttribute']);

      if (!$methodRequested) {
        throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->pathHttp\" not found");
      }

      $this->handleController($controllerRequested, $methodRequested);
    } catch (\Exception $err) {
      echo $err->getMessage();
    }
  }

  /**
   * @param array{reflectionClass: \ReflectionClass, controllerAttribute: Controller} $controllerRequested
   * @param array{methodName: string, methodAttribute: RouterMap} $methodRequested
   * @return void
   */
  private function handleController($controllerRequested, $methodRequested) {
    $controllerInstance = $controllerRequested['reflectionClass']->newInstance();

    $fullPath = $controllerRequested['controllerAttribute']->getPrefix() . $methodRequested['methodAttribute']->getPath();

    $params = Router::getParamsFromRouter($fullPath, $this->pathHttp);

    $this->requestBuilder->setParams($params);

    $this->request = $this->requestBuilder->build();

    $methodName = $methodRequested['methodName'];
    $controllerInstance = $controllerRequested['reflectionClass']->newInstance();

    $controllerInstance->$methodName($this->request, $this->response);
  }

  private function fetchControllerRequestTarget() {
    foreach ($this->routers['controllers'] as $controllerClass) {
      $controllerReflectionClass = new \ReflectionClass($controllerClass);

      $controllerReflectionAttributes = $controllerReflectionClass->getAttributes(Controller::class);

      $controllerAttribute = $this->fetchAttributeControllerRequestTarget($controllerReflectionAttributes);

      if (!$controllerAttribute) {
        continue;
      }

      return [
        'reflectionClass' => $controllerReflectionClass,
        'controllerAttribute' => $controllerAttribute,
      ];
    }

    return null;
  }

  /**
   * @param \ReflectionAttribute<Controller>[] $atributesController
   */
  private function fetchAttributeControllerRequestTarget(array $controllerReflectionAttributes): ?Controller {
    foreach ($controllerReflectionAttributes as $controllerReflectionAttribute) {
      /** @var Controller */
      $attributeControllerInstance = $controllerReflectionAttribute->newInstance();

      if (Router::isMathPrefixRouterTemplate($this->pathHttp, $attributeControllerInstance->getPrefix())) {
        return $attributeControllerInstance;
      }
    }

    return null;
  }

  private function fetchMethodRequestTargetFromController(\ReflectionClass $reflectionClass, Controller $controllerAttributes) {
    $reflectionMethods = $reflectionClass->getMethods();

    foreach ($reflectionMethods as $reflectionMethod) {
      $attributesMethod = $this->getAttributesHttpRouterFromMethod($reflectionMethod);

      foreach ($attributesMethod as $attributeMethod) {
        /** @var RouterMap */
        $attributeMethodInstance = $attributeMethod->newInstance();

        $fullPath = trim($controllerAttributes->getPrefix() . $attributeMethodInstance->getPath());

        if ($attributeMethodInstance->getMethod() !== $this->methodHttp || !Router::isMathRouterTemplate($this->pathHttp, $fullPath)) {
          continue;
        }

        return [
          'methodName' => $reflectionMethod->getName(),
          'methodAttribute' => $attributeMethodInstance,
        ];
      }
    }

    return null;
  }

  private function getAttributesHttpRouterFromMethod(\ReflectionMethod $reflectionMethod) {
    $attributesMethod = [];

    foreach (static::$HTTP_METHODS_ATTRIBUTES as $httpMethod) {
      /** @var \ReflectionAttribute<RouterMap>[] */
      $attributesMethod = array_merge($attributesMethod, $reflectionMethod->getAttributes($httpMethod));
    }

    return $attributesMethod;
  }

  /**
   * @param \ReflectionMethod $reflectionMethod
   * @return Guard[]
   */
  private function getMiddlewaresFromMethod(\ReflectionMethod $reflectionMethod) {
    return $reflectionMethod->getAttributes(Guard::class);
  }
}
