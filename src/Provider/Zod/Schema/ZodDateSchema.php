<?php

namespace App\Provider\Zod\Schema;

use App\Provider\Zod\ZodErrorValidator;

/**
 * @extends parent<string>
 */
class ZodDateSchema extends ZodSchema {
  protected static array $DATE_FORMAT = [
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
  private ?string $format = null;
  private ?string $toFormat = null;
  private ?int $min = null;
  private ?int $max = null;

  function __construct(array $attributes = null) {
    parent::__construct($attributes, 'date');
  }

  /**
   * @deprecated Coerce not working with date
   */
  #[\Override]
  function coerce(): static {
    return $this;
  }

  function format(string $format, array|string $attributes = null) {
    $this->format = $format;
    $this->addTypeValidateRule('parseFormat', $attributes);
    return $this;
  }

  function toFormat(string $toFormat, array|string $attributes = null) {
    $this->toFormat = $toFormat;
    $this->addTransformExtraRule('parseToFormat', $attributes);
    return $this;
  }

  function min(int $min, array|string $attributes = null) {
    $this->min = $min;
    $this->addRefineRule('parseMin', $attributes);
    return $this;
  }

  function max(int $max, array|string $attributes = null) {
    $this->max = $max;
    $this->addRefineRule('parseMax', $attributes);
    return $this;
  }

  /**
   * @deprecated Coerce not working with date
   */
  #[\Override]
  protected function parseCoerce($value, $attributes) {
  }

  protected function parseFormat($value, array $attributes) {
    if (is_date_format($value, $this->format))
      return;

    $this->addError($attributes['message'] ?? "Data must be in the format \"$this->format\"");
    $this->stop();
  }

  protected function parseMin($value, array $attributes) {
    if ($value >= $this->min)
      return;

    $this->addError($attributes['message'] ?? "Date must be greater than \"$this->min\"");
  }

  protected function parseMax($value, array $attributes) {
    if ($value <= $this->max)
      return;

    $this->addError($attributes['message'] ?? "Date must be less than \"$this->max\"");
  }

  protected function parseToFormat($value, array $attributes) {
    $this->value = date_format($value, $this->toFormat);
  }

  #[\Override]
  protected function isValueSameType() {
    if (!$this->value || !is_string($this->value))
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
