<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\MethodHTTP;

#[Attribute(Attribute::TARGET_METHOD)]
class Head extends RouterMap {

  function __construct(string $endpoint = '') {
    parent::__construct(MethodHTTP::HEAD->value, $endpoint);
  }
}
