<?php

namespace Core\Common\Attributes;

use Core\Enum\MethodHTTP;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Get extends RouterMap {

  function __construct(string $endpoint = '', int $statusCode = 200) {
    parent::__construct(MethodHTTP::GET->value, $endpoint, $statusCode);
  }
}
