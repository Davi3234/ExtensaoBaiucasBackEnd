<?php

namespace App\Provider\Zod;

class Z {

  /**
   * @param array{message: string}|string $attributes
   * @return ZodStringSchema
   */
  static function string(array|string $attributes = []) {
    return new ZodStringSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodNumberSchema
   */
  static function number(array|string $attributes = []) {
    return new ZodNumberSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodBooleanSchema
   */
  static function boolean(array|string $attributes = []) {
    return new ZodBooleanSchema($attributes);
  }

  /**
   * @param array{message: string}|string $attributes
   * @return ZodDateSchema
   */
  static function date(array|string $attributes = []) {
    return new ZodDateSchema($attributes);
  }

  /**
   * @param array<string, ZodSchema> $fields
   * @param array{message: string}|string $attributes
   * @return ZodObjectSchema
   */
  static function object(array $fields, array|string $attributes = []) {
    return new ZodObjectSchema($fields, $attributes);
  }

  /**
   * @param ZodSchema $fields
   * @param array{message: string}|string $attributes
   * @return ZodArraySchema
   */
  static function arrayZod(ZodSchema $schema, array|string $attributes = []) {
    return new ZodArraySchema($schema, $attributes);
  }
}
