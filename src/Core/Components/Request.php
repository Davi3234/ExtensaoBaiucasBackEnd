<?php

namespace App\Core\Components;

class Request {
  private static $instance = null;

  static function getInstance() {
    return static::$instance;
  }

  static function createRequestInstance(string $router, string $method, array $params = []) {
    self::$instance = new static;
    self::$instance->loadComponents($router, $method, $params);

    return static::$instance;
  }

  private $body = [];
  private $params = [];
  private $attributes = [];
  private $router = '/';
  private $method = '';

  private function __construct() {
  }

  private function loadComponents(string $router, string $method, array $params = []) {
    $data = self::getData();

    $this->body = $data['body'];
    $this->router = $router;
    $this->method = $method;
    $this->params = array_merge($data['params'], $params);
  }

  static function getData() {
    $dataJson = file_get_contents('php://input');
    $data = json_decode($dataJson, true);

    return [
      'body' => $data,
      'params' => $_GET,
    ];
  }

  function getParams() {
    return $this->params;
  }

  function getParam(string $name) {
    return $this->params[$name] ?: null;
  }

  function getHeaders() {
    return $this->$_SERVER;
  }

  function getHeader(string $name) {
    return $_SERVER[$name] ?: null;
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

  function getBody(string $name) {
    return $this->body[$name] ?: null;
  }

  function getAttributes() {
    return $this->attributes;
  }

  function getAttribute(string $name) {
    return $this->attributes[$name] ?: null;
  }

  function setAttribute(string $name, $value) {
    $this->attributes[$name] = $value;
  }
}
