<?php

namespace Provider\Zod\Schemas;

/**
 * @extends parent<mixed>
 */
class ZodEnumSchema extends ZodMixedSchema {

  private $valuesEnable = [];

  /**
   * @param (number|string)[] $valuesEnable
   */
  function __construct(array $valuesEnable = [], array $attributes = null) {
    parent::__construct($attributes);

    $this->valuesEnable = $valuesEnable;
    $this->addTransformRule('parseResolveValuesEnable');
  }

  protected function parseResolveValuesEnable($value, array $attributes) {
    if (in_array($value, $this->valuesEnable))
      return;

    $this->addError($attributes['message'] ?? 'Invalid value, expect "' . implode('", "', $this->valuesEnable) . "\" received $value");
  }
}
