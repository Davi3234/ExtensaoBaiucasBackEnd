<?php

namespace Provider\Zod;

use Provider\Zod\Schemas\ZodArraySchema;
use Provider\Zod\Schemas\ZodEnumSchema;
use Provider\Zod\Schemas\ZodMixedSchema;
use Provider\Zod\Schemas\ZodObjectSchema;
use Provider\Zod\Schemas\ZodStringSchema;
use Provider\Zod\Schemas\ZodNumberSchema;
use Provider\Zod\Schemas\ZodBooleanSchema;
use Provider\Zod\Schemas\ZodDateSchema;
use Provider\Zod\Schemas\ZodSchema;

class Z {

  /**
   * @param array{message: string} $attributes
   */
  static function string(array $attributes = []) {
    return new ZodStringSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   */
  static function number(array $attributes = []) {
    return new ZodNumberSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   */
  static function boolean(array $attributes = []) {
    return new ZodBooleanSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   */
  static function date(array $attributes = []) {
    return new ZodDateSchema($attributes);
  }

  /**
   * @param array<string, ZodSchema> $fields
   * @param array{message: string} $attributes
   */
  static function object(array $fields, array $attributes = []) {
    return new ZodObjectSchema($fields, $attributes);
  }

  /**
   * @param ZodSchema $fields
   * @param array{message: string} $attributes
   */
  static function arrayZod(ZodSchema $schema, array $attributes = []) {
    return new ZodArraySchema($schema, $attributes);
  }

  /**
   * @param array{message: string} $attributes
   */
  static function mixed(array $attributes = []) {
    return new ZodMixedSchema($attributes);
  }

  /**
   * @param enum-string<string|int|float> $enum
   * @param array{message: string} $attributes
   */
  static function enumNative($enum, array $attributes = []) {
    return self::enum(array_map(fn($case) => $case->value, $enum::cases()), $attributes);
  }

  /**
   * @param (number|string)[] $valuesEnable
   * @param array{message: string} $attributes
   */
  static function enum(array $valuesEnable, array $attributes = []) {
    return new ZodEnumSchema($valuesEnable, $attributes);
  }
}
