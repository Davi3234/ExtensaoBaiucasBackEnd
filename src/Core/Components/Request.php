<?php

namespace App\Core\Components;

class Request {
  private static $instance = null;

  static function getInstance() {
    return static::$instance;
  }

  static function createRequestInstance(string $router, string $method, array $params = []) {
    self::$instance = new static;
    self::$instance->loadComponents($router, $method);

    return static::$instance;
  }

  private $body = [];
  private $params = [];
  private $headers = [];
  private $attributes = [];
  private $router = '/';
  private $method = '';

  private function __construct() {
  }

  private function loadComponents($router, $method) {
    $data = self::getData();

    $this->body = $data['body'];
    $this->params = $data['params'];
    $this->headers = $data['headers'];
    $this->router = $router;
    $this->method = $method;
  }

  static function getData() {
    $dataJson = file_get_contents('php://input');
    $data = json_decode($dataJson, true);

    return [
      'body' => $data,
      'params' => $_GET,
      'headers' => $_SERVER,
    ];
  }

  function getParams() {
    return $this->params;
  }

  function getParam($name) {
    return $this->params[$name] ?: null;
  }

  function getHeaders() {
    return $this->headers;
  }

  function getHeader($name) {
    return $this->headers[$name] ?: null;
  }

  function getRouter() {
    return $this->router;
  }

  function getMethod() {
    return $this->method;
  }

  function getAllBody() {
    return $this->body;
  }

  function getBody($name) {
    return $this->body[$name] ?: null;
  }

  function getAttributes() {
    return $this->attributes;
  }

  function getAttribute($name) {
    return $this->attributes[$name] ?: null;
  }

  function setAttribute($name, $value) {
    $this->attributes[$name] = $value;
  }
}
