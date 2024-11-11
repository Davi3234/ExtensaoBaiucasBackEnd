<?php

namespace Common;

abstract class Model {

  static function __loadModel(array $raw): static {
    $instance = new static;

    return $instance;
  }
}
