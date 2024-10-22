<?php

namespace App\Core\Components\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)] // Adicione isso aqui
class Get extends RouterMap {

  function __construct(string $path = '') {
    parent::__construct('GET', $path);
  }
}
