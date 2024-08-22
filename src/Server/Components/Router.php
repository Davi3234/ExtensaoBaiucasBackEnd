<?php

namespace App\Server\Components;

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
     * GET: array<string, array{handlers: array}>,
     * POST: array<string, array{handlers: array}>,
     * PUT: array<string, array{handlers: array}>,
     * DELETE: array<string, array{handlers: array}>,
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
                throw new \Exception("Prefix router group \"{$arg['prefix']}\" already defined");

            $this->routersGroup[$arg['prefix']] = $arg;
        }
    }

    protected function createRouter($method, $path, $handlers) {
        $path = str_replace('//', '/', trim("/$path"));

        if (isset($this->routers[$method][$path]))
            throw new \Exception("Router \"$method\" \"{$path}\" already defined");

        $this->routers[$method][$path] = [
            'handlers' => $handlers,
        ];
    }

    function getRouterByPrefixMath($prefix) {
        $prefixPaths = $this->getAllPrefixRouters();

        foreach ($prefixPaths as $prefixPath) {
            if (self::isMathRouterTemplate($prefix, $prefixPath))
                return $this->getRoutersGroupByPrefix($prefixPath);
        }

        return null;
    }

    function getRoutersGroup() {
        return $this->routersGroup;
    }

    function getRoutersGroupByPrefix($prefix) {
        return $this->routersGroup[$prefix] ?: null;
    }

    /**
     * @return array<string>
     */
    function getAllPrefixRouters() {
        return array_keys($this->routersGroup);
    }

    function getRouters() {
        return $this->routers;
    }

    static function isMathRouterTemplate($routerTemplate, $router) {
        $rotaPattern = preg_replace('/:[a-zA-Z]+/', '([a-zA-Z0-9]+)', str_replace('/', '\/', $routerTemplate));

        return (bool) preg_match('/^' . $rotaPattern . '$/', $router);
    }

    static function writeRouter(...$args) {
        self::getInstance()->addRouterGroup(...$args);
    }

    static function get($path, ...$handlers) {
        self::getInstance()->createRouter('GET', $path, ...$handlers);
    }

    static function post($path, ...$handlers) {
        self::getInstance()->createRouter('POST', $path, ...$handlers);
    }

    static function put($path, ...$handlers) {
        self::getInstance()->createRouter('PUT', $path, ...$handlers);
    }

    static function delete($path, ...$handlers) {
        self::getInstance()->createRouter('DELETE', $path, ...$handlers);
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
}
