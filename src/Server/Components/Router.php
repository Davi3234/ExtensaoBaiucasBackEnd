<?php

namespace App\Core\Components;

class Router {

    /**
     * @var array<array{
     * prefix: string,
     * filePath: string 
     * }>
     */
    protected static $routersGroup = [];

    /**
     * @var array{
     * GET: array<string, array{handlers: array}>,
     * POST: array<string, array{handlers: array}>,
     * PUT: array<string, array{handlers: array}>,
     * DELETE: array<string, array{handlers: array}>,
     * }
     */
    protected static $routers = [
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
    static function writeRouter(...$args) {
        array_push(self::$routersGroup, ...$args);
    }

    static function get($path, ...$handlers) {
        self::createRouter('GET', $path, $handlers);
    }

    static function post($path, ...$handlers) {
        self::createRouter('POST', $path, $handlers);
    }

    static function put($path, ...$handlers) {
        self::createRouter('PUT', $path, $handlers);
    }

    static function delete($path, ...$handlers) {
        self::createRouter('DELETE', $path, $handlers);
    }

    protected static function createRouter($method, $path, $handlers) {
        self::$routers[$method][$path] = [
            'handlers' => $handlers,
        ];
    }
}
