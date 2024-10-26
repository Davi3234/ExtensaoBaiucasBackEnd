<?php

namespace Provider\Zod\Schema;

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
  #[\Override]
  function coerce(): static {
    return $this;
  }

  /**
   * @deprecated Coerce not working with date
   */
  #[\Override]
  protected function parseCoerce($value, $attributes) {
  }

  #[\Override]
  protected function isValueSameType() {
    return true;
  }
}
