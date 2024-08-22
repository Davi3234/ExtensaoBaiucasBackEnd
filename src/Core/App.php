<?php

namespace App\Core;

use App\Core\Components\Router;

class App {

    private static $instance = null;

    /**
     * @return App
     */
    static function getInstance() {
        if (!self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

    protected $path = '';
    protected $method = '';

    /**
     * @var Router
     */
    protected $router = null;

    private function __construct() {
        $this->router = Router::getInstance();
    }

    protected function resolveRequest() {
        if (!$this->method)
            exit('Not found');

        $router = $this->router->getRouterByPrefixMath($this->method, $this->path);

        if (!$router)
            exit('Not found');

        foreach ($router['handlers'] as $handler) {
            $controller = $handler[0];
            $methodAction = $handler[1];

            if (!class_exists($controller))
                continue;

            if (empty($methodAction) || !method_exists($controller, $methodAction))
                continue;

            (new $controller)->$methodAction();
        }
    }

    static function Bootstrap() {
        $path = '/';

        isset($_GET['url']) && $path .= $_GET['url'];

        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

        self::makeApp($path, $method);
        self::$instance->resolveRequest();
    }

    protected static function makeApp($path, $method) {
        App::getInstance();

        self::$instance->method = $method;
        self::$instance->path = $path;
    }
}
