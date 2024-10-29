<?php

namespace Core\Common\Attributes;

use Core\Common\Middleware;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Guard {

  /**
   * @param class-string<Middleware> $middleware
   */
  function __construct(
    private readonly string $middleware
  ) {
  }

  function getMiddleware() {
    return $this->middleware;
  }
}
