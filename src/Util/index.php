<?php

function formatter_path($path) {
  return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function log_message($message) {
  error_log(json_encode($message), 3, 'logs/log.txt');
}

function remove_string($search, $subject) {
  return str_replace($search, '', $subject);
}

function remove_start_str($search, $subject) {
  if (!$search)
    return $subject;

  if (is_starts_with($search, $subject))
    return substr($subject, strlen($search));

  return $subject;
}

function remove_end_str($search, $subject) {
  if (is_ends_with($search, $subject))
    return substr($subject, 0, -strlen($search));

  return $subject;
}

function is_starts_with($search, $subject) {
  return strpos($search, $subject) === 0;
}

function is_ends_with($search, $subject) {
  return strpos($search, $subject) === strlen($search);
}

function set_env($name, $value) {
  putenv("$name=$value");
}

function get_env($name) {
  return getenv($name) ?: null;
}

function is_date_format($date, $format = 'Y-m-d') {
  $dateTime = DateTime::createFromFormat($format, $date);

  return $dateTime && strtolower($dateTime->format($format)) === strtolower($date);
}

function uuid() {
  return time() . '-' . mt_rand();
}
