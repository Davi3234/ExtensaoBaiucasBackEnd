<?php

namespace App\Core\Components;

class Request {
  private static $instance = null;

  static function getInstance($router = '', $method = '') {
    if (!isset(self::$instance))
      self::$instance = new self($router, $method);

    return self::$instance;
  }

  private $body = [];
  private $params = [];
  private $headers = [];
  private $attributes = [];
  private $router = '/';
  private $method = '';

  private function __construct($router = '', $method = '') {
    $dataJson = file_get_contents('php://input');
    $data = json_decode($dataJson, true);

    $this->body = $data;
    $this->params = $_GET;
    $this->headers = $_SERVER;
    $this->router = $router;
    $this->method = $method;
  }

  function loadBody($body) {
    $this->body = $body;
  }

  function setParam($param, $value) {
    $this->params[$param] = $value;
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
    if (isset($this->getAllBody()[$name]))
      return $this->getAllBody()[$name];

    return null;
  }

  function getAttributes() {
    return $this->attributes;
  }

  function getAttribute($name) {
    return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
  }

  function setAttribute($name, $value) {
    $this->attributes[$name] = $value;
  }
}
