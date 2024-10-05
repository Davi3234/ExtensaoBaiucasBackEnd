<?php

namespace App\Provider\Zod\Schema;

use App\Provider\Zod\ZodErrorValidator;

/**
 * @extends parent<array>
 */
class ZodArraySchema extends ZodSchema {

  protected ZodSchema $schema;
  private int|float|null $min = null;
  private int|float|null $max = null;
  protected int|float|null $length = null;

  /**
   * @var ?callable
   */
  protected $_filterCallable = null;

  /**
   * @var ?callable
   */
  protected $_mapCallable = null;

  function __construct(ZodSchema $schema, array $attributes = null) {
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

  function filter(callable $value) {
    $this->_filterCallable = $value;
    $this->addTransformExtraRule('parseFilter');
    return $this;
  }

  function map(callable $value, array|string $attributes = null) {
    $this->_mapCallable = $value;
    $this->addTransformExtraRule('parseMap', $attributes);
    return $this;
  }

  protected function parseResolveValuesSchema() {
    $valueRaw = [];

    foreach ($this->value as $index => $value) {
      $result = $this->schema->parseSafe($value);

      if (!$result['errors'])
        $valueRaw[$index] = $result['data'];
      else {
        foreach ($result['errors'] as $error)
          $this->addError(new ZodErrorValidator($error['message'], $index . ($error['path'] ? '.' . $error['path'][0] : '')));
      }
    }

    $this->value = $valueRaw;
  }

  #[\Override]
  protected function parseCoerce($value, array $attributes) {
    if (is_object($value))
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

  protected function parseFilter($value, array $attributes) {
    $filter = $this->_filterCallable;

    if (!$filter || !is_callable($filter))
      return;

    $this->value = array_filter($value, $filter);
  }

  protected function parseMap($value, array $attributes) {
    $map = $this->_mapCallable;

    if (!$map || !is_callable($map))
      return;

    $this->value = array_map($map, $value);
  }
}
