<?php

namespace App\Provider\Zod;

class Z {

  /**
   * @param array{message: string}|string $attributes
   * @return ZodStringSchema
   */
  static function string($attributes = []) {
    return new ZodStringSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodNumberSchema
   */
  static function number($attributes = []) {
    return new ZodNumberSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodBooleanSchema
   */
  static function boolean($attributes = []) {
    return new ZodBooleanSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodDateSchema
   */
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

  /**
   * @param ZodSchema $fields
   * @param array{message: string}|string $attributes
   * @return ZodArraySchema
   */
  static function arrayZod($schema, $attributes = []) {
    return new ZodArraySchema($schema, $attributes);
  }
}