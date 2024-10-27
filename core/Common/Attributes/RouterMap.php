<?php

namespace Core\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RouterMap {

  private readonly string $endpoint;

  function __construct(
    private readonly string $method,
    string $endpoint = ''
  ) {
    $endpoint = trim($endpoint);

    if ($endpoint == '/') {
      $endpoint = '';
    }

    $this->endpoint = $endpoint;
  }

  function getMethod() {
    return $this->method;
  }

  function getEndpoint() {
    return $this->endpoint;
  }
}
