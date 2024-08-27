<?php

namespace App\Service\Zod;

class ZodNumberSchema extends ZodSchema {

  private $gt = null;
  private $gte = null;
  private $lt = null;
  private $lte = null;
  private $multipleOf = null;

  function __construct($attributes = null) {
    parent::__construct($attributes, 'number');
  }

  function gt($value, $attributes = null) {
    $this->gt = $value;
    $this->addRefineRule('parseGt', $attributes);
    return $this;
  }

  function gte($value, $attributes = null) {
    $this->gte = $value;
    $this->addRefineRule('parseGte', $attributes);
    return $this;
  }

  function lt($value, $attributes = null) {
    $this->lt = $value;
    $this->addRefineRule('parseLt', $attributes);
    return $this;
  }

  function lte($value, $attributes = null) {
    $this->lte = $value;
    $this->addRefineRule('parseLte', $attributes);
    return $this;
  }

  function int() {
    $this->type = 'integer';
    return $this;
  }

  function positive($attributes = null) {
    $this->addRefineRule('parsePositive', $attributes);
    return $this;
  }

  function nonnegative($attributes = null) {
    $this->addRefineRule('parseNonnegative', $attributes);
    return $this;
  }

  function negative($attributes = null) {
    $this->addRefineRule('parseNegative', $attributes);
    return $this;
  }

  function nonpositive($attributes = null) {
    $this->addRefineRule('parseNonpositive', $attributes);
    return $this;
  }

  function multipleOf($value, $attributes = null) {
    $this->multipleOf = $value;
    $this->addRefineRule('parseMultipleOf', $attributes);
    return $this;
  }

  protected function parseCoerce($value, $attributes) {
    if ($this->isValueEmpty())
      return;

    if ($this->type == 'number')
      $this->value = (float)$value;
    else if ($this->type == 'integer')
      $this->value = (int)$value;
  }

  protected function parseInt($value, $attributes) {
    if (is_integer($value))
      return;

    $type = gettype($value);

    $this->addError(new ZodErrorValidator(isset($attributes['invalidType']) ? $attributes['invalidType'] : "Expect an \"integer\" received \"$type\""));
  }

  protected function parseGt($value, $attributes) {
    if ($value > $this->gt)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must to be greater than \"$this->gt\""));
  }

  protected function parseGte($value, $attributes) {
    if ($value >= $this->gte)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must to be greater or equal than \"$this->gte\""));
  }

  protected function parseLt($value, $attributes) {
    if ($value < $this->lt)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must to be less than \"$this->lt\""));
  }

  protected function parseLte($value, $attributes) {
    if ($value <= $this->lte)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must to be less or equal than \"$this->lte\""));
  }

  protected function parsePositive($value, $attributes) {
    if ($value > 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must be positive"));
  }

  protected function parseNonpositive($value, $attributes) {
    if ($value <= 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number cannot be positive"));
  }

  protected function parseNegative($value, $attributes) {
    if ($value < 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must be negative"));
  }

  protected function parseNonnegative($value, $attributes) {
    if ($value >= 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number cannot be negative"));
  }

  protected function parseMultipleOf($value, $attributes) {
    if ($value % $this->multipleOf == 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Number must be a multiple of \"$this->multipleOf\""));
  }

  protected function isValueSameType() {
    return !is_string($this->value) && is_numeric($this->value);
  }

  protected function isValueEmpty() {
    return is_null($this->value) || $this->value === '';
  }
}
