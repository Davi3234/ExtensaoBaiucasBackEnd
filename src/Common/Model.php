<?php

namespace App\Common;

abstract class Model {

  static function _loadModel(array $raw): static {
    $instance = new static;
    $instance->_load($raw);

    return $instance;
  }

  abstract protected function _load(array $raw);
}
