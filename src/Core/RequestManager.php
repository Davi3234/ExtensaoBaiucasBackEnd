<?php

namespace App\Core;

use App\Core\Components\Attribute\Controller;
use App\Core\Components\Attribute\Get;
use App\Core\Components\Attribute\Guard;
use App\Core\Components\Attribute\Post;
use App\Core\Components\Attribute\RouterMap;
use App\Core\Components\Router;
use App\Exception\Http\RouterNotFoundException;

class RequestManager {

  private static $HTTP_METHODS_ATTRIBUTES = [Get::class, Post::class];

  /** @var class-string */
  private string $controllerClassRequested;
  private Controller $controllerAttribute;

  private string $methodControllerRequested;
  private RouterMap $routerMapAttribute;

  function __construct(
    private array $routersMap = [],
    private string $pathHttp = '',
    private string $methodHttp = ''
  ) {
  }

  function loadRequest() {
    $this->loadControllerRequestTarget();
    $this->loadMethodRequestTargetFromController();
    return $this->loadHandlers();
  }

  private function loadControllerRequestTarget() {
    foreach ($this->routersMap['controllers'] as $controllerClass) {
      $controllerReflectionClass = new \ReflectionClass($controllerClass);

      $controllerReflectionAttributes = $controllerReflectionClass->getAttributes(Controller::class);

      $controllerAttribute = $this->getAttributeControllerRequestTarget($controllerReflectionAttributes);

      if (!$controllerAttribute) {
        continue;
      }

      $this->controllerClassRequested = $controllerClass;
      $this->controllerAttribute = $controllerAttribute;

      return;
    }

    throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->pathHttp\" not found");
  }

  private function loadMethodRequestTargetFromController() {
    $reflectionClass = $this->getReflectionClassRequested();

    $reflectionMethods = $reflectionClass->getMethods();

    $attributeMethodRequested = $this->findMethodAndAttributeMethodRequested($reflectionMethods);

    if (!$attributeMethodRequested) {
      throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->pathHttp\" not found");
    }

    $this->methodControllerRequested = $attributeMethodRequested['method']->getName();
    $this->routerMapAttribute = $attributeMethodRequested['attribute']->newInstance();
  }

  private function loadHandlers() {
    $middlewares = $this->getMiddlewaresFromRouterMap();

    $handlers = array_map(function ($middleware) {
      return [
        'controller' => $middleware->getName(),
        'method' => 'perform',
      ];
    }, $middlewares);

    $handlers[] = [
      'controller' => $this->controllerClassRequested,
      'method' => $this->methodControllerRequested,
    ];

    return $handlers;
  }

  /**
   * @param \ReflectionMethod<RouterMap>[] $reflectionMethods
   */
  private function findMethodAndAttributeMethodRequested(array $reflectionMethods) {
    foreach ($reflectionMethods as $reflectionMethod) {
      $reflectionAttributes = $this->getAttributesHttpRouterFromMethod($reflectionMethod);

      foreach ($reflectionAttributes as $reflectionAttribute) {
        if ($this->isAttributeMethodRequested($reflectionAttribute)) {
          return [
            'method' => $reflectionMethod,
            'attribute' => $reflectionAttribute,
          ];
        }
      }
    }

    return null;
  }

  /**
   * @param \ReflectionAttribute<RouterMap> $attributeMethod
   */
  private function isAttributeMethodRequested(\ReflectionAttribute $attributeMethod) {
    /** @var RouterMap */
    $attributeMethodInstance = $attributeMethod->newInstance();

    $fullPath = trim($this->controllerAttribute->getPrefix() . $attributeMethodInstance->getPath());

    return $attributeMethodInstance->getMethod() === $this->methodHttp && Router::isMathRouterTemplate($this->pathHttp, $fullPath);
  }

  /**
   * @param \ReflectionAttribute<Controller>[] $atributesController
   */
  private function getAttributeControllerRequestTarget(array $controllerReflectionAttributes): ?Controller {
    foreach ($controllerReflectionAttributes as $controllerReflectionAttribute) {
      /** @var Controller */
      $attributeControllerInstance = $controllerReflectionAttribute->newInstance();

      if (Router::isMathPrefixRouterTemplate($this->pathHttp, $attributeControllerInstance->getPrefix())) {
        return $attributeControllerInstance;
      }
    }

    return null;
  }

  private static function getAttributesHttpRouterFromMethod(\ReflectionMethod $reflectionMethod) {
    $attributesMethod = [];

    foreach (static::$HTTP_METHODS_ATTRIBUTES as $httpMethod) {
      /** @var \ReflectionAttribute<RouterMap>[] */
      $attributesMethod = array_merge($attributesMethod, $reflectionMethod->getAttributes($httpMethod));
    }

    return $attributesMethod;
  }

  private function getMiddlewaresFromRouterMap() {
    return $this->getReflectionClassRequested()->getMethod($this->methodControllerRequested)->getAttributes(Guard::class);
  }

  function getReflectionClassRequested() {
    return new \ReflectionClass($this->controllerClassRequested);
  }

  function isRequestedControllerFound() {
    return !!$this->controllerClassRequested;
  }

  function getFullPathRequested() {
    return trim($this->controllerAttribute->getPrefix() . $this->routerMapAttribute->getPath());
  }
}
