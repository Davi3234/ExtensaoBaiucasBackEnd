<?php

namespace App\Core\Components;

use App\Exception\InternalServerErrorException;

class Router {

    private static $instance = null;

    /**
     * @return Router
     */
    static function getInstance() {
        if (!self::$instance)
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * @var array<string, array{
     * prefix: string,
     * filePath: string 
     * }>
     */
    protected $routersGroup = [];

    /**
     * @var array{
     * GET: array<string, array{router: string, handlers: array}>,
     * POST: array<string, array{router: string, handlers: array}>,
     * PUT: array<string, array{router: string, handlers: array}>,
     * DELETE: array<string, array{router: string, handlers: array}>,
     * }
     */
    protected $routers = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     * @param array<array{
     * prefix: string,
     * filePath: string 
     * }> ...$args
     * @return void
     */
    function addRouterGroup(...$args) {
        foreach ($args as $arg) {
            if (isset($this->routersGroup[$arg['prefix']]))
                throw new InternalServerErrorException("Prefix router group \"{$arg['prefix']}\" already defined");

            $this->routersGroup[$arg['prefix']] = $arg;
        }
    }

    protected function createRouter($method, $path, $handlers) {
        $path = str_replace('//', '/', trim("/$path"));

        foreach ($handlers as &$handler) {
            if (!is_array($handler))
                $handler = [$handler];
        }

        if (isset($this->routers[$method][$path]))
            throw new InternalServerErrorException("Router \"$method\" \"{$path}\" already defined");

        $this->routers[$method][$path] = [
            'router' => $path,
            'handlers' => $handlers,
        ];
    }

    function getRouteRequested($method, $routerRequest) {
        $routerGroup = $this->getRouterGroupByRouter($routerRequest);

        if (!$routerGroup)
            return null;

        @include str_replace('\\', '/', __DIR__ . '/../../' . $routerGroup['filePath']);

        $router = $this->getRouterByMethodAndRouter($method, $routerRequest);

        return $router;
    }

    function getRouterGroupByRouter($router) {
        foreach ($this->routersGroup as $prefix => $routerGroup) {
            if (self::isMathPrefixRouterTemplate($prefix, $router)) {
                return $routerGroup;
            }
        }

        return null;
    }

    function getRoutersGroup() {
        return $this->routersGroup;
    }

    /**
     * @return array<string>
     */
    function getAllRoutersPaths($method) {
        return array_keys($this->routers[$method]);
    }

    function getRouters() {
        return $this->routers;
    }
    function getRouterByMethodAndRouter($method, $routerPath) {
        foreach ($this->routers[$method] as $prefix => $router) {
            if (self::isMathRouterTemplate($prefix, $routerPath)) {
                return $router;
            }
        }

        return null;
    }

    function getRoutersByPrefix($method, $prefix) {
        return $this->routers[$method][$prefix] ?: null;
    }

    static function isMathPrefixRouterTemplate($routerTemplate, $router) {
        $pattern = self::getPatternRouterMatching($routerTemplate);

        return preg_match('/^' . $pattern . '/', $router);
    }

    static function isMathRouterTemplate($routerTemplate, $router) {
        $pattern = self::getPatternRouterMatching($routerTemplate);

        return preg_match('/^' . $pattern . '$/', $router);
    }

    static function getParamsFromRouter($routerTemplate, $router) {
        preg_match_all('/:([a-zA-Z]+)/', $routerTemplate, $params);
        $params = $params[1];

        $pattern = self::getPatternRouterMatching($routerTemplate);

        if (preg_match('/^' . $pattern . '$/', $router, $matches)) {
            array_shift($matches);
            return array_combine($params, $matches);
        }

        return [];
    }

    static function getPatternRouterMatching($routerTemplate) {
        return preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $routerTemplate));
    }

    static function writeRouter(...$args) {
        self::getInstance()->addRouterGroup(...$args);
    }

    static function get($path, ...$handlers) {
        self::getInstance()->createRouter('GET', $path, $handlers);
    }

    static function post($path, ...$handlers) {
        self::getInstance()->createRouter('POST', $path, $handlers);
    }

    static function put($path, ...$handlers) {
        self::getInstance()->createRouter('PUT', $path, $handlers);
    }

    static function patch($path, ...$handlers) {
        self::getInstance()->createRouter('PATCH', $path, $handlers);
    }

    static function delete($path, ...$handlers) {
        self::getInstance()->createRouter('DELETE', $path, $handlers);
    }

    static function head($path, ...$handlers) {
        self::getInstance()->createRouter('HEAD', $path, $handlers);
    }

    static function options($path, ...$handlers) {
        self::getInstance()->createRouter('OPTIONS', $path, $handlers);
    }

    static function maker($prefix = '') {
        return new RouterMake($prefix);
    }
}

class RouterMake {
    private $prefix = '';

    function __construct($prefix = '') {
        $this->prefix = $prefix;
    }

    function get($path, ...$handlers) {
        Router::get($this->prefix . $path, ...$handlers);
    }

    function post($path, ...$handlers) {
        Router::post($this->prefix . $path, ...$handlers);
    }

    function put($path, ...$handlers) {
        Router::put($this->prefix . $path, ...$handlers);
    }

    function delete($path, ...$handlers) {
        Router::delete($this->prefix . $path, ...$handlers);
    }

    function patch($path, ...$handlers) {
        Router::patch($path, $handlers);
    }


    function head($path, ...$handlers) {
        Router::head($path, $handlers);
    }

    function options($path, ...$handlers) {
        Router::options($path, $handlers);
    }
}
