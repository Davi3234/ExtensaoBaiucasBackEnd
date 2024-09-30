<?php

namespace App\Provider\Zod\Schema;

use App\Provider\Zod\ZodErrorValidator;

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

    $this->$valuesEnable = $valuesEnable;
    $this->addTransformRule('parseResolveValuesEnable');
  }

  protected function parseResolveValuesEnable($value, array $attributes) {
    if (in_array($value, $this->valuesEnable))
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? ""));
  }
}
