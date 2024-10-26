<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\MethodHTTP;

#[Attribute(Attribute::TARGET_METHOD)]
class Patch extends RouterMap {

  function __construct(string $endpoint = '') {
    parent::__construct(MethodHTTP::PATCH->value, $endpoint);
  }
}
