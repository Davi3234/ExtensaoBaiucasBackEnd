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

class Server {

  private static $instance;
  private RequestBuilder $requestBuilder;
  private Request $request;
  private Response $response;
  private string $pathHttp = '';
  private string $methodHttp = '';

  /**
   * @var array{controllers: class-string[]} $routers
   */
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
      $controllersRequested = $this->getRequestTargetControllers();

      foreach ($controllersRequested as $controllerRequested) {
        $reflectionClass = $controllerRequested['reflection'];
        $controllerAttributes = $controllerRequested['controllerAttributes'];

        $methodsRequested = $this->getRequestTargetMethodsFromReflectionClass($reflectionClass, $controllerAttributes);

        foreach ($methodsRequested as $methodRequested) {
          $methodName = $methodRequested['methodName'];

          $fullPath = $controllerRequested['controllerAttributes']->getPrefix() . $methodRequested['methodAttribute']->getPath();

          $params = Router::getParamsFromRouter($fullPath, $this->pathHttp);

          $this->requestBuilder->setParams($params);

          $this->request = $this->requestBuilder->build();

          $guards = $this->getMiddlewaresFromMethod($methodRequested['reflection']);

          foreach ($guards as $guard) {

            $middleware = $guard->getMiddleware();

            $middlewareInstance = new $middleware();

            $middlewareInstance->perform($this->request, $this->response);
          }

          $controllerInstance = $reflectionClass->newInstance();

          $response = $controllerInstance->$methodName($this->request, $this->response);
        }
      }
    } catch (\Exception $err) {
      echo $err->getMessage();
    }
  }

  function getRequestTargetControllers() {
    $controllersRequested = [];

    foreach ($this->routers['controllers'] as $classController) {
      $reflectionClass = new \ReflectionClass($classController);

      $atributesController = $reflectionClass->getAttributes(Controller::class);

      foreach ($atributesController as $attributeController) {
        $attributeControllerInstance = $attributeController->newInstance();

        if (!$attributeControllerInstance instanceof Controller) {
          continue;
        }

        $controllerPrefix = $attributeControllerInstance->getPrefix();

        if (!Router::isMathPrefixRouterTemplate($this->pathHttp, $controllerPrefix)) {
          continue;
        }

        $controllersRequested[] = [
          'classController' => $classController,
          'reflection' => $reflectionClass,
          'controllerAttributes' => $attributeControllerInstance
        ];
      }
    }

    return $controllersRequested;
  }

  function getRequestTargetMethodsFromReflectionClass(\ReflectionClass $reflectionClass, Controller $controllerAttributes) {
    $methodsRequested = [];

    $methods = $reflectionClass->getMethods();

    foreach ($methods as $method) {
      $attributesMethod = [
        ...$method->getAttributes(Get::class),
        ...$method->getAttributes(Post::class),
      ];

      foreach ($attributesMethod as $attribute) {
        $attributeMethodInstance = $attribute->newInstance();

        if (!$attributeMethodInstance instanceof RouterMap) {
          continue;
        }

        $fullPath = $controllerAttributes->getPrefix() . $attributeMethodInstance->getPath();

        if ($attributeMethodInstance->getMethod() !== $this->methodHttp || !Router::isMathRouterTemplate($this->pathHttp, $fullPath)) {
          continue;
        }

        $methodsRequested[] = [
          'reflection' => $method,
          'methodName' => $method->getName(),
          'methodAttribute' => $attributeMethodInstance,
        ];

        return $methodsRequested;
      }
    }

    return $methodsRequested;
  }

  /**
   * @param \ReflectionMethod $reflectionMethod
   * @return Guard[]
   */
  function getMiddlewaresFromMethod(\ReflectionMethod $reflectionMethod) {
    return $reflectionMethod->getAttributes(Guard::class);
  }
}
