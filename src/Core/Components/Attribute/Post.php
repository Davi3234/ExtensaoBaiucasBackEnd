<?php

namespace App\Core\Components\Attribute;

class Post extends RouterMap {

  function __construct(string $path = '') {
    parent::__construct('POST', $path);
  }
}
