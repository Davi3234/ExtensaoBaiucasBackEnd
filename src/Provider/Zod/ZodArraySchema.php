<?php

namespace App\Provider\Zod;

class ZodArraySchema extends ZodSchema {

  protected $schema;
  private $min = null;
  private $max = null;
  protected $length = null;

  function __construct($schema, $attributes = null) {
    parent::__construct($attributes, 'array');

    $this->schema = $schema;
    $this->addTransformRule('parseResolveValuesSchema');
  }

  function nonempty($attributes = null) {
    $this->addRefineRule('parseNonempty', $attributes);
    return $this;
  }

  function min($min, $attributes = null) {
    $this->min = $min;
    $this->addRefineRule('parseMin', $attributes);
    return $this;
  }

  function max($max, $attributes = null) {
    $this->max = $max;
    $this->addRefineRule('parseMax', $attributes);
    return $this;
  }

  function length($value, $attributes = null) {
    $this->length = $value;
    $this->addRefineRule('parseLength', $attributes);
    return $this;
  }

  protected function parseResolveValuesSchema() {
    $valueRaw = [];

    foreach($this->value as $index => $value) {
      $result = $this->schema->parse($value);

      if (!isset($result['errors']))
        $valueRaw[$index] = $result['data'];
      else {
        foreach($result['errors'] as $error)
          $this->addError(new ZodErrorValidator($error['message'], $index.($error['path'] ? '.'.$error['path'][0] : '')));
      }
    }

    $this->value = $valueRaw;
  }

  protected function parseCoerce($value, $attributes) {
    if (!is_object($value))
      return;

    $this->value = (array) $value;
  }

  protected function parseNonempty($value, $attributes) {
    if ($value)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array cannot be empty"));
  }

    protected function parseMin($value, $attributes) {
    if (count($value) >= $this->min)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->min\" or more items"));
  }

  protected function parseMax($value, $attributes) {
    if (count($value) <= $this->max)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->max\" or fewer items"));
  }

  protected function parseLength($value, $attributes) {
    if (count($value) == $this->length)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Array must contain \"$this->length\" items exactly"));
  }
}
