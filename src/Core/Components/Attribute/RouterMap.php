<?php

namespace App\Core\Components\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RouterMap {

  function __construct(
    private readonly string $method,
    private readonly string $path = ''
  ) {
  }

  function getMethod() {
    return $this->method;
  }

  function getPath() {
    return $this->path;
  }
}
