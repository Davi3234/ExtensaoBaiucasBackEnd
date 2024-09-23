<?php

namespace App\Common;

abstract class Model {

  /**
   * @return static
   */
  static function _loadModel(array $raw) {
    $instance = new static;
    $instance->_load($raw);

    return $instance;
  }

  abstract protected function _load(array $raw);
}
