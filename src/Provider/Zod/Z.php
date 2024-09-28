<?php

namespace App\Provider\Zod;

class Z {

  /**
   * @param array{message: string} $attributes
   * @return ZodStringSchema
   */
  static function string(array $attributes = []) {
    return new ZodStringSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   * @return ZodNumberSchema
   */
  static function number(array $attributes = []) {
    return new ZodNumberSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   * @return ZodBooleanSchema
   */
  static function boolean(array $attributes = []) {
    return new ZodBooleanSchema($attributes);
  }

  /**
   * @param array{message: string} $attributes
   * @return ZodDateSchema
   */
  static function date(array $attributes = []) {
    return new ZodDateSchema($attributes);
  }

  /**
   * @param array<string, ZodSchema> $fields
   * @param array{message: string} $attributes
   * @return ZodObjectSchema
   */
  static function object(array $fields, array $attributes = []) {
    return new ZodObjectSchema($fields, $attributes);
  }

  /**
   * @param ZodSchema $fields
   * @param array{message: string} $attributes
   * @return ZodArraySchema
   */
  static function arrayZod(ZodSchema $schema, array $attributes = []) {
    return new ZodArraySchema($schema, $attributes);
  }
}
