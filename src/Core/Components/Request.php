<?php

namespace App\Core\Components;

class Request {

  function __construct(
    private readonly array $body = [],
    private readonly array $params = [],
    private array $attributes = [],
    private readonly string $router = '/',
    private readonly string $method = ''
  ) {
  }

  function getParams() {
    return $this->params;
  }

  function getParam(string $name) {
    return $this->params[$name] ?: null;
  }

  function getHeaders() {
    return $_SERVER;
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

  static function getPathHttpRequested() {
    if (!isset($_GET['url']))
      $_GET['url'] = $_SERVER['PATH_INFO'];

    if (!$_GET['url'])
      $_GET['url'] = '/';

    $_GET['url'] = str_replace('//', '/', $_GET['url']);

    return $_GET['url'];
  }

  static function getMethodHttpRequested() {
    return $_SERVER['REQUEST_METHOD'] ?? null;
  }

  static function getBodyRequest() {
    $dataJson = file_get_contents('php://input');
    return json_decode($dataJson, true) ?? [];
  }

  static function getParamsRequest() {
    return $_GET ?? [];
  }
}
