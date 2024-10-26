<?php

function log_message($message) {
  error_log(json_encode($message), 3, 'logs/log.txt');
}

function remove_string(string $search, string $subject) {
  return str_replace($search, '', $subject);
}

function remove_start_str(string $search, string $subject) {
  if (!$search)
    return $subject;

  if (str_starts_with($search, $subject))
    return substr($subject, strlen($search));

  return $subject;
}

function remove_end_str(string $search, string $subject) {
  if (str_ends_with($search, $subject))
    return substr($subject, 0, -strlen($search));

  return $subject;
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
