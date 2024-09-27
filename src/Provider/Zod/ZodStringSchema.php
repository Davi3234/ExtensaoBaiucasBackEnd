<?php

namespace App\Provider\Zod;

class ZodStringSchema extends ZodSchema {

  protected ?int $length = null;
  protected ?int $min = null;
  protected ?int $max = null;
  protected ?string $includes = null;
  protected ?string $startsWith = null;
  protected ?string $endsWith = null;
  protected ?string $regex = null;

  function __construct(array $attributes = null) {
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

  function length(int $value, array $attributes = null) {
    $this->length = $value;
    $this->addRefineRule('parseLength', $attributes);
    return $this;
  }

  function min(int $value, array $attributes = null) {
    $this->min = $value;
    $this->addRefineRule('parseMin', $attributes);
    return $this;
  }

  function max(int $value, array $attributes = null) {
    $this->max = $value;
    $this->addRefineRule('parseMax', $attributes);
    return $this;
  }

  function includes(string $value, array $attributes = null) {
    $this->includes = $value;
    $this->addRefineRule('parseIncludes', $attributes);
    return $this;
  }

  function startsWith(string $value, array $attributes = null) {
    $this->startsWith = $value;
    $this->addRefineRule('parseStartsWith', $attributes);
    return $this;
  }

  function endsWith(string $value, array $attributes = null) {
    $this->endsWith = $value;
    $this->addRefineRule('parseEndsWith', $attributes);
    return $this;
  }

  function regex(string $value, array $attributes = null) {
    $this->regex = $value;
    $this->addRefineRule('parseRegex', $attributes);
    return $this;
  }

  protected function parseCoerce($value, array $attributes) {
    $this->value = (string) $value;
  }

  protected function parseTrim($value, array $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = trim($value);
  }

  protected function parseToUpperCase($value, array $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = strtoupper($value);
  }

  protected function parseToLowerCase($value, array $attributes) {
    if (!$this->isValueSameType())
      return;

    $this->value = strtolower($value);
  }

  protected function parseLength($value, array $attributes) {
    if (strlen($value) == $this->length)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must have $this->length characters"));
  }

  protected function parseMin($value, array $attributes) {
    if (strlen($value) >= $this->min)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must have at least $this->min characters"));
  }

  protected function parseMax($value, array $attributes) {
    if (strlen($value) <= $this->max)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must have a maximum of $this->max characters"));
  }

  protected function parseIncludes($value, array $attributes) {
    if (strpos($value, $this->includes) !== false)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must contain the text \"$this->includes\""));
  }

  protected function parseStartsWith($value, array $attributes) {
    if (strpos($value, $this->startsWith) === 0)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must starts with \"$this->startsWith\""));
  }

  protected function parseEndsWith($value, array $attributes) {
    $length = strlen($this->endsWith);

    if (!$length || substr($value, -$length) === $this->endsWith)
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? "Value must ends with \"$this->endsWith\""));
  }

  protected function parseRegex($value, array $attributes) {
    if (preg_match($this->regex, $value))
      return;

    $this->addError(new ZodErrorValidator($attributes['message'] ?? 'Value invalid'));
  }

  protected function isValueEmpty() {
    return is_null($this->value) || $this->value === '';
  }
}
