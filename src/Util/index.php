<?php

function formatter_path(string $path) {
  return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function log_message($message) {
  error_log(json_encode($message), 3, 'logs/log.txt');
}

function remove_string(string $search, string $subject) {
  return str_replace($search, '', $subject);
}

function remove_start_str(string $search, string $subject) {
  if (!$search)
    return $subject;

  if (is_starts_with($search, $subject))
    return substr($subject, strlen($search));

  return $subject;
}

function remove_end_str(string $search, string $subject) {
  if (is_ends_with($search, $subject))
    return substr($subject, 0, -strlen($search));

  return $subject;
}

function is_starts_with(string $search, string $subject) {
  return strpos($search, $subject) === 0;
}

function is_ends_with(string $search, string $subject) {
  return strpos($search, $subject) === strlen($search);
}

function is_decimal($val) {
  return is_numeric($val) && floor($val) != $val;
}

function set_env($name, $value) {
  putenv("$name=$value");
}

function get_env(string $name, $valueDefault = null) {
  return getenv($name) ?: $valueDefault;
}

function is_date_format($date, string $format) {
  $dateTime = DateTime::createFromFormat($format, $date);

  return $dateTime && strtolower($dateTime->format($format)) === strtolower($date);
}

function uuid() {
  return time() . '-' . mt_rand();
}

function console(...$args) {
  @ini_set('default_mimetype', 'text/html');

?><script>
    console.log(...<?= json_encode($args) ?>)
  </script><?php
          }
