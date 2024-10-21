<?php

namespace App\Core;

use App\Core\Components\Attribute\Controller;
use ReflectionClass;

class Server {

  private static $instance;

  static function getInstance(): static {
    return self::$instance;
  }

  /**
   * @param array{controllers: class-string[]} $routers
   */
  static function bootstrap(array $routers) {
    foreach ($routers['controllers'] as $controller) {
      $reflectionClass = new ReflectionClass($controller);

      $atributesController = $reflectionClass->getAttributes(Controller::class);

      foreach ($atributesController as $attributeController) {
        $controllerPrefix = $attributeController->getArguments()[0];

        preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $controllerPrefix));
      }
    }
  }
}
