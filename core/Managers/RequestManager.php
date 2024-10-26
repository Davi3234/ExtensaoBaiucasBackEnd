<?php

namespace Core\Managers;

use Exception\HTTP\RouterNotFoundException;
use Core\HTTP\RouterURL;
use Core\Common\Attributes;

class RequestManager {

  private static $HTTP_METHODS_ATTRIBUTES = [
    Attributes\Get::class,
    Attributes\Post::class,
    Attributes\Put::class,
    Attributes\Delete::class,
    Attributes\Head::class,
    Attributes\Patch::class,
    Attributes\Options::class,
  ];

  /** @var class-string */
  private string $controllerClassRequested;
  private Attributes\Controller $controllerAttribute;

  private string $methodControllerRequested;
  private Attributes\RouterMap $routerMapAttribute;

  function __construct(
    private array $routersMap = [],
    private string $routerHttp = '',
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

      $controllerReflectionAttributes = $controllerReflectionClass->getAttributes(Attributes\Controller::class);

      $controllerAttribute = $this->getAttributeControllerRequestTarget($controllerReflectionAttributes);

      if (!$controllerAttribute) {
        continue;
      }

      $this->controllerClassRequested = $controllerClass;
      $this->controllerAttribute = $controllerAttribute;

      return;
    }

    throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->routerHttp\" not found");
  }

  private function loadMethodRequestTargetFromController() {
    $reflectionClass = $this->getReflectionClassRequested();

    $reflectionMethods = $reflectionClass->getMethods();

    $attributeMethodRequested = $this->findMethodAndAttributeMethodRequested($reflectionMethods);

    if (!$attributeMethodRequested) {
      throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->routerHttp\" not found");
    }

    $this->methodControllerRequested = $attributeMethodRequested['method']->getName();
    $this->routerMapAttribute = $attributeMethodRequested['attribute']->newInstance();
  }

  private function loadHandlers() {
    $middlewares = $this->getMiddlewaresFromRouterMap();

    $handlers = array_map(function ($middleware) {
      return [
        'controller' => $middleware->newInstance()->getMiddleware(),
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
   * @param \ReflectionMethod<Attributes\RouterMap>[] $reflectionMethods
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
   * @param \ReflectionAttribute<Attributes\RouterMap> $attributeMethod
   */
  private function isAttributeMethodRequested(\ReflectionAttribute $attributeMethod) {
    /** @var Attributes\RouterMap */
    $attributeMethodInstance = $attributeMethod->newInstance();

    $endpoint = trim($this->controllerAttribute->getPrefix() . $attributeMethodInstance->getEndpoint()) ?: '/';

    return $attributeMethodInstance->getMethod() === $this->methodHttp && RouterURL::isMathRouter($this->routerHttp, $endpoint);
  }

  /**
   * @param \ReflectionAttribute<Attributes\Controller>[] $atributesController
   */
  private function getAttributeControllerRequestTarget(array $controllerReflectionAttributes): ?Attributes\Controller {
    foreach ($controllerReflectionAttributes as $controllerReflectionAttribute) {
      /** @var Attributes\Controller */
      $attributeControllerInstance = $controllerReflectionAttribute->newInstance();

      if (RouterURL::isMathPrefixRouter($this->routerHttp, $attributeControllerInstance->getPrefix())) {
        return $attributeControllerInstance;
      }
    }

    return null;
  }

  private static function getAttributesHttpRouterFromMethod(\ReflectionMethod $reflectionMethod) {
    $attributesMethod = [];

    foreach (static::$HTTP_METHODS_ATTRIBUTES as $httpMethod) {
      /** @var \ReflectionAttribute<Attributes\RouterMap>[] */
      $attributesMethod = array_merge($attributesMethod, $reflectionMethod->getAttributes($httpMethod));
    }

    return $attributesMethod;
  }

  private function getMiddlewaresFromRouterMap() {
    return $this->getReflectionClassRequested()->getMethod($this->methodControllerRequested)->getAttributes(Attributes\Guard::class);
  }

  function getReflectionClassRequested() {
    return new \ReflectionClass($this->controllerClassRequested);
  }

  function isRequestedControllerFound() {
    return !!$this->controllerClassRequested;
  }

  function getEndpointRequested() {
    return trim($this->controllerAttribute->getPrefix() . $this->routerMapAttribute->getEndpoint());
  }
}
