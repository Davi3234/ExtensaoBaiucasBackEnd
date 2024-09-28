<?php

namespace App\Provider\Zod;

class ZodBooleanSchema extends ZodSchema {

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'boolean');
  }

  protected function parseCoerce($value, array $attributes) {
    $this->value = (bool)$value;
  }

  function parseNoSafe($value): bool {
    /**
     * @var bool
     */
    $result = parent::_parseNoSafe($value);
    return $result;
  }
  
  /**
   * @return array{data: ?bool, errors: ?array<string|int, array{message: mixed, path: mixed}>}
   */
  function parseSafe($value): array {
    return parent::_parseSafe($value);
  }
}
