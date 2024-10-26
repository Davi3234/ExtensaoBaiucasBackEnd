<?php

namespace Core\Common\Attributes;

use Attribute;
use Core\Enum\RouterMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class Post extends RouterMap {

  function __construct(string $endpoint = '') {
    parent::__construct(RouterMethod::POST->value, $endpoint);
  }
}
