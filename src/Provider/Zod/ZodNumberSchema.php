<?php

namespace App\Provider\Zod;

/**
 * @extends parent<number>
 */
class ZodNumberSchema extends ZodSchema {

  private int|float|null $gt = null;
  private int|float|null $gte = null;
  private int|float|null $lt = null;
  private int|float|null $lte = null;
  private int|float|null $multipleOf = null;

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'number');
  }

  function gt(int|float $value, array|string $attributes = null) {
    $this->gt = $value;
    $this->addRefineRule('parseGt', $attributes);
    return $this;
  }

  function gte(int|float $value, array|string $attributes = null) {
    $this->gte = $value;
    $this->addRefineRule('parseGte', $attributes);
    return $this;
  }

  function lt(int|float $value, array|string $attributes = null) {
    $this->lt = $value;
    $this->addRefineRule('parseLt', $attributes);
    return $this;
  }

  function lte(int|float $value, array|string $attributes = null) {
    $this->lte = $value;
    $this->addRefineRule('parseLte', $attributes);
    return $this;
  }

  function int() {
    $this->type = 'integer';
    return $this;
  }

  function positive(array|string $attributes = null) {
    $this->addRefineRule('parsePositive', $attributes);
    return $this;
  }

  function nonnegative(array|string $attributes = null) {
    $this->addRefineRule('parseNonnegative', $attributes);
    return $this;
  }

  function negative(array|string $attributes = null) {
    $this->addRefineRule('parseNegative', $attributes);
    return $this;
  }

  function nonpositive(array|string $attributes = null) {
    $this->addRefineRule('parseNonpositive', $attributes);
    return $this;
  }

  function multipleOf($value, array|string $attributes = null) {
    $this->multipleOf = $value;
    $this->addRefineRule('parseMultipleOf', $attributes);
    return $this;
  }

  protected function parseCoerce($value, array $attributes) {
    if ($this->isValueEmpty())
      return;

    if (is_decimal($value))
      $this->value = (float)$value;
    else
      $this->value = (int)$value;
  }

  protected function parseInt($value, array $attributes) {
    if (is_integer($value))
      return;

    $type = gettype($value);

    $this->addError(new ZodErrorValidator($attributes['invalidType'] ?? "Expect an \"integer\" received \"$type\""));
  }

  protected function parseGt($value, array $attributes) {
    if ($value > $this->gt)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must to be greater than \"$this->gt\""));
  }

  protected function parseGte($value, array $attributes) {
    if ($value >= $this->gte)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must to be greater or equal than \"$this->gte\""));
  }

  protected function parseLt($value, array $attributes) {
    if ($value < $this->lt)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must to be less than \"$this->lt\""));
  }

  protected function parseLte($value, array $attributes) {
    if ($value <= $this->lte)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must to be less or equal than \"$this->lte\""));
  }

  protected function parsePositive($value, array $attributes) {
    if ($value > 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must be positive"));
  }

  protected function parseNonpositive($value, array $attributes) {
    if ($value <= 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number cannot be positive"));
  }

  protected function parseNegative($value, array $attributes) {
    if ($value < 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must be negative"));
  }

  protected function parseNonnegative($value, array $attributes) {
    if ($value >= 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number cannot be negative"));
  }

  protected function parseMultipleOf($value, array $attributes) {
    if ($value % $this->multipleOf == 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Number must be a multiple of \"$this->multipleOf\""));
  }

  protected function isValueSameType() {
    return !is_string($this->value) && is_numeric($this->value);
  }

  protected function isValueEmpty() {
    return is_null($this->value) || $this->value === '';
  }
}
