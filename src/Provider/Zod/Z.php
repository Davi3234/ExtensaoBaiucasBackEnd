<?php

namespace App\Provider\Zod;

class Z {

  static function string($attributes = []) {
    return new ZodStringSchema($attributes);
  }

  static function number($attributes = []) {
    return new ZodNumberSchema($attributes);
  }

  static function boolean($attributes = []) {
    return new ZodBooleanSchema($attributes);
  }

  static function date($attributes = []) {
    return new ZodDateSchema($attributes);
  }

  /**
   * @param array<ZodSchema> $fields
   * @param array{message: string}|string $attributes
   * @return ZodObjectSchema
   */
  static function object($fields, $attributes = []) {
    return new ZodObjectSchema($fields, $attributes);
  }

  static function arrayZod($schema, $attributes = []) {
    return new ZodArraySchema($schema, $attributes);
  }
}