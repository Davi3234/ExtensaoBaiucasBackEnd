<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\MethodHTTP;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Delete extends RouterMap {

  function __construct(string $endpoint = '') {
    parent::__construct(MethodHTTP::DELETE->value, $endpoint);
  }
}
