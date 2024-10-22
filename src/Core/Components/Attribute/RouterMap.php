<?php

namespace App\Core\Components\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RouterMap {

  function __construct(
    public readonly string $method,
    public readonly string $path = ''
  ) {
  }

  function getMethod() {
    return $this->method;
  }

  function getPath() {
    return $this->path;
  }
}
