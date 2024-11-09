<?php

namespace Core\Managers;

use Provider\IO\File;
use Core\Common\Attributes;
use Core\Common\Attributes\Guard;
use Core\Exception\HTTP\RouterNotFoundException;
use Core\HTTP\RouterURL;

class RequestManager {

  private const PATH_STORAGE_ROUTERS_DIR = PATH_STORAGE . '/cache/app';
  private const PATH_STORAGE_ROUTERS_FILE = '/routers.php';

  private static $HTTP_METHODS_ATTRIBUTES = [
    Attributes\Get::class,
    Attributes\Post::class,
    Attributes\Put::class,
    Attributes\Delete::class,
    Attributes\Head::class,
    Attributes\Patch::class,
    Attributes\Options::class,
  ];

  /**
   * @var array{endpoint: string, controller: string, middlewares: string[]}
   */
  private ?array $endpointRequested = null;

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  function __construct(
    private array $routersMap = [],
    private string $routerHttp = '',
    private string $methodHttp = ''
  ) {
  }

  function loadRequest() {
    $this->loadEndpointFromCacheFile();
    return $this->loadHandlers();
  }

  function loadEndpointFromCacheFile() {
    $endpoint = $this->getEndpointRequestedFromCacheFile();

    if (!$endpoint)
      throw new RouterNotFoundException("Router \"$this->methodHttp\" \"$this->routerHttp\" not fount");

    $this->endpointRequested = $endpoint;
  }

  function getEndpointRequestedFromCacheFile() {
    $endpoints = $this->getEndpointsFromCacheFile();

    $endpointsMethod = $endpoints[$this->methodHttp] ?? [];

    return array_find(function ($router) {
      return RouterURL::isMathRouter($this->routerHttp, $router['endpoint']);
    }, $endpointsMethod);
  }

  function getEndpointsFromCacheFile() {
    $pathFile = $this->getFullPathFileCacheRouters();

    $endpoints = [];
    if (!file_exists($pathFile))
      $endpoints = $this->storageEndpoints($this->routersMap);
    else
      $endpoints = require $pathFile;

    return $endpoints['routers'] ?? [];
  }

  function loadHandlers() {
    $handlers = array_map(function ($middleware) {
      [$controller, $method] = explode('::', $middleware);

      return [
        'controller' => $controller,
        'method' => $method
      ];
    }, $this->endpointRequested['middlewares']);

    [$controller, $method] = explode('::', $this->endpointRequested['controller']);

    $handlers[] = [
      'controller' => $controller,
      'method' => $method
    ];

    return $handlers;
  }

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  static function storageEndpoints($routers = []) {
    $endpoints = self::listAllEndpoints($routers);
    $routers = ['routers' => $endpoints];
    $dataRouters = var_export($routers, true);

    $dataFile = <<<EOL
    <?php
    //File cache auto created
    return $dataRouters;
    EOL;

    (new File(self::PATH_STORAGE_ROUTERS_DIR, self::PATH_STORAGE_ROUTERS_FILE))->write($dataFile);

    return $routers;
  }

  /**
   * @param array{controllers: class-string[]} $routersMap
   */
  static function listAllEndpoints($routers = []) {
    $endpoints = [];

    foreach ($routers['controllers'] as $controllerClass) {
      $controllerReflectionClass = new \ReflectionClass($controllerClass);

      $controllerReflectionAttributes = $controllerReflectionClass->getAttributes(Attributes\Controller::class);

      foreach ($controllerReflectionAttributes as $controllerReflectionAttribute) {
        /** @var Attributes\Controller */
        $attributeControllerInstance = $controllerReflectionAttribute->newInstance();

        $prefixEndpoint = $attributeControllerInstance->getPrefix();

        $reflectionMethods = $controllerReflectionClass->getMethods();

        foreach ($reflectionMethods as $reflectionMethod) {
          $reflectionAttributes = self::getAttributesHttpRouterFromMethod($reflectionMethod);

          foreach ($reflectionAttributes as $reflectionAttribute) {
            /** @var Attributes\RouterMap */
            $routerMap = $reflectionAttribute->newInstance();

            $suffixEndpoint = $routerMap->getEndpoint();

            $endpoint = $prefixEndpoint . $suffixEndpoint;

            $attributeGuards = $reflectionMethod->getAttributes(Guard::class);

            $middlewares = array_map(function ($attribute) {
              /** @var Guard */
              $guard = $attribute->newInstance();
              return "{$guard->getMiddleware()}::perform";
            }, $attributeGuards);

            if (!$endpoints[$routerMap->getMethod()])
              $endpoints[$routerMap->getMethod()] = [];

            $endpoints[$routerMap->getMethod()][] = [
              'endpoint' => $endpoint,
              'controller' => "$controllerClass::{$reflectionMethod->getName()}",
              'middlewares' => $middlewares,
            ];
          }
        }
      }
    }

    foreach ($endpoints as &$routers) {
      $routers = static::orderEndpoints($routers);
    }

    return $endpoints;
  }

  private static function getAttributesHttpRouterFromMethod(\ReflectionMethod $reflectionMethod) {
    $attributesMethod = [];

    foreach (static::$HTTP_METHODS_ATTRIBUTES as $httpMethod) {
      /** @var \ReflectionAttribute<Attributes\RouterMap>[] */
      $attributesMethod = array_merge($attributesMethod, $reflectionMethod->getAttributes($httpMethod));
    }

    return $attributesMethod;
  }

  static function orderEndpoints(array $endpoints) {
    uksort($endpoints, function ($a, $b) use ($endpoints) {
      $hasParamA = str_contains($endpoints[$a]['endpoint'], ':');
      $hasParamB = str_contains($endpoints[$b]['endpoint'], ':');

      if ($hasParamA === $hasParamB)
        return strcmp($a, $b);

      return $hasParamA ? 1 : -1;
    });

    return $endpoints;
  }

  function getEndpointRequested() {
    return $this->endpointRequested;
  }

  private function getFullPathFileCacheRouters() {
    return path_normalize(self::PATH_STORAGE_ROUTERS_DIR . self::PATH_STORAGE_ROUTERS_FILE);
  }
}
