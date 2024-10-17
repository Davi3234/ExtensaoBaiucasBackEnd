<?php

namespace App\Provider\Zod\Schema;

/**
 * @extends parent<bool>
 */
class ZodBooleanSchema extends ZodSchema {

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'boolean');
  }

  #[\Override]
  protected function parseCoerce($value, array $attributes) {
    $this->value = (bool)$value;
  }
}
