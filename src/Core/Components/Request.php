<?php

namespace App\Core\Components;

use function App\Util\remove_start_str;

class Request
{
    private static $instance = null;
    private $body = [];
    private $params = [];
    private $headers = [];
    private $attributes = [];
    private $router = '/';
    private $method = '';

    static function getInstance($router = '', $method = '') {
        if (!isset(self::$instance))
            self::$instance = new self($router, $method);

        return self::$instance;
    }

    private function __construct($router = '', $method = '') {
        $dataJson = file_get_contents('php://input');
        $data = json_decode($dataJson, true);

        $this->body = $data;
        $this->params = $_REQUEST;
        $this->headers = $_SERVER;
        $this->router = $router;
        $this->method = $method;
    }

    function loadBody($body) {
        $this->body = $body;
    }

    function loadParams($params) {
        $this->params = $params;
    }

    function loadHeaders($headers) {
        $this->headers = $headers;
    }

    function getParams() {
        return $this->params;
    }

    function getParam($name) {
        if (isset($this->getParams()[$name]))
            return $this->getParams()[$name];

        return null;
    }

    function getHeaders() {
        return $this->headers;
    }

    function getHeader($name) {
        if (isset($this->getHeaders()[$name]))
            return $this->getHeaders()[$name];

        return null;
    }

    function getAllBody() {
        return $this->body;
    }

    function getBody($name) {
        if (isset($this->getAllBody()[$name]))
            return $this->getAllBody()[$name];

        return null;
    }

    function getAttributes() {
        return $this->attributes;
    }

    function getAttribute($name) {
        if (isset($this->getAttributes()[$name]))
            return $this->getAttributes()[$name];

        return null;
    }

    function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
    }
}