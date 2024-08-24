<?php

namespace App\Service\Sql;

class SQLFormat {

  /**
   * A list of data types that are allowed for casting.
   * @var array<string>
   */
  private static $TYPES_CAST = ['text', 'int', 'numeric', 'decimal', 'float', 'boolean', 'date', 'time', 'timestamp', 'json', 'interval'];

  /**
   * Converts the provided value to a string suitable for database insertion.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string.
   */
  static function toString($value) {
    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `NULL`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string or `NULL`.
   */
  static function toStringOrNull($value) {
    return self::toStringFormatOrNull($value);
  }

  /**
   * Converts the provided value to a string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `DEFAULT`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string or `DEFAULT`.
   */
  static function toStringOrDefault($value) {
    return self::toStringFormatOrDefault($value);
  }

  /**
   * Converts the provided value to a date string suitable for database insertion.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a date string.
   */
  static function toDate($value) {
    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a date string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `NULL`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a date string or `NULL`.
   */
  static function toDateOrNull($value) {
    return self::toStringFormatOrNull($value);
  }

  /**
   * Converts the provided value to a date string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `DEFAULT`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a date string or `DEFAULT`.
   */
  static function toDateOrDefault($value) {
    return self::toStringFormatOrDefault($value);
  }

  /**
   * Converts the provided value to a time string suitable for database insertion.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a time string.
   */
  static function toTime($value) {
    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a time string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `NULL`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a time string or `NULL`.
   */
  static function toTimeOrNull($value) {
    return self::toStringFormatOrNull($value);
  }

  /**
   * Converts the provided value to a time string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `DEFAULT`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a time string or `DEFAULT`.
   */
  static function toTimeOrDefault($value) {
    return self::toStringFormatOrDefault($value);
  }

  /**
   * Converts the provided value to a timestamp string suitable for database insertion.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a timestamp string.
   */
  static function toTimestamp($value) {
    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a timestamp string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `NULL`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a timestamp string or `NULL`.
   */
  static function toTimestampOrNull($value) {
    return self::toStringFormatOrNull($value);
  }

  /**
   * Converts the provided value to a timestamp string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `DEFAULT`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a timestamp string or `DEFAULT`.
   */
  static function toTimestampOrDefault($value) {
    return self::toStringFormatOrDefault($value);
  }

  /**
   * Converts the provided value to a boolean expression suitable for database logic.
   * `Truthy` values are converted to `TRUE`, and `Falsy` values are converted to `FALSE`.
   * @param string|numeric|boolean $value The value to be evaluated.
   * @return string The boolean expression as a string (`TRUE` or `FALSE`).
   */
  static function toBooleanExpression($value) {
    return ((bool)$value) ? 'TRUE' : 'FALSE';
  }

  /**
   * Converts the provided value to a string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `NULL`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string or `NULL`.
   */
  static function toStringFormatOrNull($value) {
    if (!$value)
      return self::sqlNull();

    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a string suitable for database insertion.
   * If the value is equivalent to `FALSE`, it returns `DEFAULT`.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string or `DEFAULT`.
   */
  static function toStringFormatOrDefault($value) {
    if (!$value)
      return self::sqlDefault();

    return self::toStringFormat($value);
  }

  /**
   * Converts the provided value to a string suitable for database insertion.
   * @param string|numeric|boolean $value The value to be converted.
   * @return string The value converted to a string.
   */
  static function toStringFormat($value) {
    return "'$value'";
  }

  /**
   * Returns the string `DEFAULT` to represent a default value in SQL.
   * @return string The SQL `DEFAULT` keyword.
   */
  static function sqlDefault() {
    return 'DEFAULT';
  }

  /**
   * Returns the string `NULL` to represent a null value in SQL.
   * @return string The SQL `NULL` keyword.
   */
  static function sqlNull() {
    return 'NULL';
  }

  /**
   * Casts a field or value to the specified target type in SQL.
   * @param string|numeric $value The field or value to be cast.
   * @param string $target The target data type (e.g., text, int, numeric, etc.).
   * @return string The value cast to the specified type in SQL.
   */
  static function cast($value, $target) {
    if (!in_array(strtolower($target), self::$TYPES_CAST))
      return $value;

    return $value . '::' . strtoupper($target);
  }
}
