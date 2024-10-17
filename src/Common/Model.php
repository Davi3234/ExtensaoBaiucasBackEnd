<?php

namespace App\Common;

abstract class Model {

  static function __loadModel(array $raw): static {
    $instance = new static;
    $instance->__load($raw);

    return $instance;
  }

  abstract protected function __load(array $raw);
}
