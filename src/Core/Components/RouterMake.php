<?php

namespace App\Core\Components;

class RouterMake {
  private $prefix = '';

  function __construct($prefix = '') {
    $this->prefix = $prefix;
  }

  function get(string $path, ...$handlers) {
    Router::get($this->createPath($path), ...$handlers);
  }

  function post(string $path, ...$handlers) {
    Router::post($this->createPath($path), ...$handlers);
  }

  function put(string $path, ...$handlers) {
    Router::put($this->createPath($path), ...$handlers);
  }

  function delete(string $path, ...$handlers) {
    Router::delete($this->createPath($path), ...$handlers);
  }

  function patch(string $path, ...$handlers) {
    Router::patch($this->createPath($path), $handlers);
  }


  function head(string $path, ...$handlers) {
    Router::head($this->createPath($path), $handlers);
  }

  function options(string $path, ...$handlers) {
    Router::options($this->createPath($path), $handlers);
  }

  protected function createPath(string $path) {
    if (!$path || $path == '/')
      $path = '';

    return $this->prefix . $path;
  }
}
