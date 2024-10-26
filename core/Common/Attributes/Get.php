<?php

namespace Core\Common\Attributes;

use Core\Enum\RouterMethod;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Get extends RouterMap {

  function __construct(string $path = '') {
    parent::__construct(RouterMethod::GET->value, $path);
  }
}
