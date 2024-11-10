<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\MethodHTTP;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Head extends RouterMap {

  function __construct(string $endpoint = '', int $statusCode = 200) {
    parent::__construct(MethodHTTP::HEAD->value, $endpoint, $statusCode);
  }
}
