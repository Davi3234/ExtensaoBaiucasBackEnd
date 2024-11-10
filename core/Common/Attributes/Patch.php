<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\MethodHTTP;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Patch extends RouterMap {

  function __construct(string $endpoint = '', int $statusCode = 200) {
    parent::__construct(MethodHTTP::PATCH->value, $endpoint, $statusCode);
  }
}
