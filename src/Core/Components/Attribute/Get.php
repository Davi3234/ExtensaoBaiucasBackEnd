<?php

namespace App\Core\Components\Attribute;

class Get extends RouterMap {

  function __construct(string $path = '') {
    parent::__construct('GET', $path);
  }
}
