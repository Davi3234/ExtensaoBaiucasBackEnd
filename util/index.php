<?php

function log_message($message) {
  error_log(json_encode($message), 3, 'logs/log.txt');
}

function remove_string(string $search, string $subject) {
  return str_replace($search, '', $subject);
}

function str_remove_start(string $search, string $subject) {
  if (!$search)
    return $subject;

  if (str_starts_with($search, $subject))
    return substr($subject, strlen($search));

  return $subject;
}

function str_remove_end(string $search, string $subject) {
  if (str_ends_with($subject, $search))
    return substr($subject, 0, -strlen($search));

  return $subject;
}

function str_remove_preg(array|string $search, string $subject) {
  return preg_replace($search, '', $subject);
}

function is_decimal($val) {
  return is_numeric($val) && floor($val) != $val;
}

function env(string $name, $valueDefault = null) {
  return getenv($name) ?: $_ENV[$name] ?: $valueDefault;
}

function is_date_format($date, string $format) {
  $dateTime = DateTime::createFromFormat($format, $date);

  return $dateTime && strtolower($dateTime->format($format)) === strtolower($date);
}

function str_bool(bool $value) {
  return $value ? 'true' : 'false';
}


/**
 * @template TKey of array-key
 * @template TValue
 * 
 * @param (callable(TValue $value): bool)|(callable(TValue $value, TKey $key): bool) $callback
 * @param array<TKey, TValue> $array
 * @return ?TValue
 */
function array_find(callable $callback, array $array): mixed {
  foreach ($array as $key => $value) {
    if ($callback($value, $key))
      return $value;
  }

  return null;
}

/**
 * @template TKey of array-key
 * @template TValue
 * 
 * @param array<TKey, TValue> $array
 * @param (callable(TValue $value): bool)|(callable(TValue $value, TKey $key): bool) $callback
 * @return bool
 */
function array_any(array $array, callable $callback) {
  foreach ($array as $value) {
    if ($callback($value))
      return true;
  }

  return false;
}

/**
 * @template TKey of array-key
 * @template TValue
 * 
 * @param array<TKey, TValue> $array
 * @param (callable(TValue $value): bool)|(callable(TValue $value, TKey $key): bool) $callback
 * @return bool
 */
function array_all(array $array, callable $callback) {
  foreach ($array as $value) {
    if (!$callback($value))
      return false;
  }

  return true;
}

function uuid() {
  return sprintf(
    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff)
  );
}

/**
 * @param string[] $paths
 * @return string
 */
function path_join(...$paths) {
  return implode(DIRECTORY_SEPARATOR, $paths);
}

function path_normalize(string $path): string {
  return preg_replace('/[\\/]/', DIRECTORY_SEPARATOR, $path);
}
