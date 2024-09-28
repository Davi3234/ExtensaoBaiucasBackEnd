<?php

namespace App\Provider\Zod;

/**
 * @extends parent<array>
 */
class ZodArraySchema extends ZodSchema {

  protected ZodArraySchema $schema;
  private int|float|null $min = null;
  private int|float|null $max = null;
  protected int|float|null $length = null;

  function __construct(ZodArraySchema $schema, array $attributes = null) {
    parent::__construct($attributes, 'array');

    $this->schema = $schema;
    $this->addTransformRule('parseResolveValuesSchema');
  }

  function nonempty(array|string $attributes = null) {
    $this->addRefineRule('parseNonempty', $attributes);
    return $this;
  }

  function min(int|float $min, array|string $attributes = null) {
    $this->min = $min;
    $this->addRefineRule('parseMin', $attributes);
    return $this;
  }

  function max(int|float $max, array|string $attributes = null) {
    $this->max = $max;
    $this->addRefineRule('parseMax', $attributes);
    return $this;
  }

  function length(int|float $value, array|string $attributes = null) {
    $this->length = $value;
    $this->addRefineRule('parseLength', $attributes);
    return $this;
  }

  protected function parseResolveValuesSchema() {
    $valueRaw = [];

    foreach ($this->value as $index => $value) {
      $result = $this->schema->parseSafe($value);

      if (!isset($result['errors']))
        $valueRaw[$index] = $result['data'];
      else {
        foreach ($result['errors'] as $error)
          $this->addError(new ZodErrorValidator($error['message'], $index . ($error['path'] ? '.' . $error['path'][0] : '')));
      }
    }

    $this->value = $valueRaw;
  }

  protected function parseCoerce($value, array $attributes) {
    if (!is_object($value))
      return;

    $this->value = (array) $value;
  }

  protected function parseNonempty($value, array $attributes) {
    if ($value)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array cannot be empty"));
  }

  protected function parseMin($value, array $attributes) {
    if (count($value) >= $this->min)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->min\" or more items"));
  }

  protected function parseMax($value, array $attributes) {
    if (count($value) <= $this->max)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->max\" or fewer items"));
  }

  protected function parseLength($value, array $attributes) {
    if (count($value) == $this->length)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->length\" items exactly"));
  }
}
