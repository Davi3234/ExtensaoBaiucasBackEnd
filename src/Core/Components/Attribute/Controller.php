<?php

namespace App\Core\Components\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller {

  function __construct(
    private readonly string $prefix = ''
  ) {
  }

  function getPrefix() {
    return $this->prefix;
  }
}