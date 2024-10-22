<?php

namespace App\Core\Components\Attribute;

use App\Core\Components\Middleware;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Guard {

  /**
   * @param class-string<Middleware> $middleware
   */
  function __construct(
    public readonly string $middleware
  ) {
  }

  function getMiddleware() {
    return $this->middleware;
  }
}
