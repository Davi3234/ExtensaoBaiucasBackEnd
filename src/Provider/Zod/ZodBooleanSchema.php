<?php

namespace App\Provider\Zod;

class ZodBooleanSchema extends ZodSchema {

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'boolean');
  }

  protected function parseCoerce($value, array $attributes) {
    $this->value = (bool)$value;
  }
}
