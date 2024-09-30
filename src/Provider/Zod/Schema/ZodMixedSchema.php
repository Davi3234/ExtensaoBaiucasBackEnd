<?php

namespace App\Provider\Zod\Schema;

/**
 * @extends parent<mixed>
 */
class ZodMixedSchema extends ZodSchema {

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'mixed');
  }

  /**
   * @deprecated Coerce not working with date
   */
  function coerce(): static {
    return $this;
  }

  /**
   * @deprecated Coerce not working with date
   */
  protected function parseCoerce($value, $attributes) {
  }

  protected function isValueSameType() {
    return true;
  }
}
