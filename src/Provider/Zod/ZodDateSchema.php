<?php

namespace App\Provider\Zod;

class ZodDateSchema extends ZodSchema {
  protected static $DATE_FORMAT = [
    'Y-m-d',       // 2024-08-18
    'd/m/Y',       // 18/08/2024
    'm/d/Y',       // 08/18/2024
    'Y-m-d H:i:s', // 2024-08-18 14:30:45
    'd/m/Y H:i:s', // 18/08/2024 14:30:45
    'm/d/Y H:i:s', // 08/18/2024 14:30:45
    'd-m-Y H:i:s', // 18-08-2024 14:30:45
    'm-d-Y H:i:s', // 08-18-2024 14:30:45
    'd-m-Y',       // 18-08-2024
    'm-d-Y',       // 08-18-2024
    'H:i:s',       // 14:30:45
    'H:i',         // 14:30
    'H',          // 14
  ];
  private $format = null;
  private $toFormat = null;
  private $min = null;
  private $max = null;

  function __construct($attributes = null) {
    parent::__construct($attributes, 'date');
  }

  /**
   * @deprecated Coerce not working with date
   * @return static
   */
  function coerce() {
    parent::coerce();
    return $this;
  }

  function format($format, $attributes = null) {
    $this->format = $format;
    $this->addTypeValidateRule('parseFormat', $attributes);
    return $this;
  }

  function toFormat($toFormat, $attributes = null) {
    $this->toFormat = $toFormat;
    $this->addTransformExtraRule('parseToFormat', $attributes);
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

  protected function parseCoerce($value, $attributes) {
  }

  protected function parseFormat($value, $attributes) {
    if (is_date_format($value, $this->format))
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Data must be in the format \"$this->format\""));
    $this->stop();
  }

  protected function parseMin($value, $attributes) {
    if ($value >= $this->min)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Date must be greater than \"$this->min\""));
  }

  protected function parseMax($value, $attributes) {
    if ($value <= $this->max)
      return;

    $this->addError(new ZodErrorValidator(isset($attributes['message']) ? $attributes['message'] : "Date must be less than \"$this->max\""));
  }

  protected function parseToFormat($value, $attributes) {
    $this->value = date_format($value, $this->toFormat);
  }

  protected function isValueSameType() {
    if (!$this->value)
      return false;

    if (!is_string($this->value))
      return false;

    if ($this->format && is_date_format($this->value, $this->format))
      return true;

    foreach (self::$DATE_FORMAT as $format) {
      if (is_date_format($this->value, $format))
        return true;
    }

    return false;
  }
}
