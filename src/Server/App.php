<?php

namespace App\Server;

use App\Server\Components\Router;

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
        $this->router->getRouterByPrefixMath($this->path);
    }

    static function Bootstrap($request) {
        $path = explode('?', $request['REQUEST_URI'])[0];
        $method = $request['REQUEST_METHOD'];

        self::makeApp($path, $method);
        self::$instance->resolveRequest();
    }

    protected static function makeApp($path, $method) {
        App::getInstance();

        self::$instance->method = $method;
        self::$instance->path = $path;
    }
}
