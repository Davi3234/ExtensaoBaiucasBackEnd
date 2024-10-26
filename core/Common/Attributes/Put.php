<?php

namespace Core\Common\Attributes;

use Core\Enum\MethodHTTP;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Put extends RouterMap {

  function __construct(string $endpoint = '') {
    parent::__construct(MethodHTTP::PUT->value, $endpoint);
  }
}
