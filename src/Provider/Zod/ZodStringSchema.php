<?php

namespace App\Provider\Zod;

class ZodStringSchema extends ZodSchema {

  protected $length = null;
  protected $min = null;
  protected $max = null;
  protected $includes = null;
  protected $startsWith = null;
  protected $endsWith = null;
  protected $regex = null;

  function __construct($attributes = null) {
    parent::__construct($attributes, 'string');
  }

  function trim() {
    $this->addTransformRule('parseTrim');
    return $this;
  }

  function toLowerCase() {
    $this->addTransformRule('parseToLowerCase');
    return $this;
  }

  function toUpperCase() {
    $this->addTransformRule('parseToUpperCase');
    return $this;
  }

  function length($value, $attributes = null) {
    $this->length = $value;
    $this->addRefineRule('parseLength', $attributes);
    return $this;
  }

  function min($value, $attributes = null) {
    $this->min = $value;
    $this->addRefineRule('parseMin', $attributes);
    return $this;
  }

  function max($value, $attributes = null) {
    $this->max = $value;
    $this->addRefineRule('parseMax', $attributes);
    return $this;
  }

  function includes($value, $attributes = null) {
    $this->includes = $value;
    $this->addRefineRule('parseIncludes', $attributes);
    return $this;
  }

  function startsWith($value, $attributes = null) {
    $this->startsWith = $value;
    $this->addRefineRule('parseStartsWith', $attributes);
    return $this;
  }

  function endsWith($value, $attributes = null) {
    $this->endsWith = $value;
    $this->addRefineRule('parseEndsWith', $attributes);
    return $this;
  }

  function regex($value, $attributes = null) {
    $this->regex = $value;
    $this->addRefineRule('parseRegex', $attributes);
    return $this;
  }

  protected function parseCoerce($value, $attributes) {
    $this->value = (string) $value;
  }

  protected function parseTrim($value, $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = trim($value);
  }

  protected function parseToUpperCase($value, $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = strtoupper($value);
  }

  protected function parseToLowerCase($value, $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = strtolower($value);
  }

  protected function parseLength($value, $attributes) {
    if (strlen($value) == $this->length)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must have $this->length characters"));
  }

  protected function parseMin($value, $attributes) {
    if (strlen($value) >= $this->min)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must have at least $this->min characters"));
  }

  protected function parseMax($value, $attributes) {
    if (strlen($value) <= $this->max)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must have a maximum of $this->max characters"));
  }

  protected function parseIncludes($value, $attributes) {
    if (strpos($value, $this->includes) !== false)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must contain the text \"$this->includes\""));
  }

  protected function parseStartsWith($value, $attributes) {
    if (strpos($value, $this->startsWith) === 0)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must starts with \"$this->startsWith\""));
  }

  protected function parseEndsWith($value, $attributes) {
    $length = strlen($this->endsWith);

    if (!$length || substr($value, -$length) === $this->endsWith)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Value must ends with \"$this->endsWith\""));
  }

  protected function parseRegex($value, $attributes) {
    if (preg_match($this->regex, $value))
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : 'Value invalid'));
  }

  protected function isValueEmpty() {
    return is_null($this->value) || $this->value === '';
  }
}
