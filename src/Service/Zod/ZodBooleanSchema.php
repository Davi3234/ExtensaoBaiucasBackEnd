<?php

namespace App\Service\Zod;

class ZodBooleanSchema extends ZodSchema {

  function __construct($attributes = null) {
    parent::__construct($attributes, 'boolean');
  }

  protected function parseCoerce($value, $attributes) {
    $this->value = (bool)$value;
  }
}