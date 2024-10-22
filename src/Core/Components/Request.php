<?php

namespace App\Core\Components;

class Request {

  function __construct(
    private $body = [],
    private $params = [],
    private $attributes = [],
    private $router = '/',
    private $method = ''
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
}
