<?php

namespace App\Util;

function line() {
    echo '<br>';
}

function formatterPath($path) {
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

function arrayHasAttributes(array $array, ...$attributes) {
    foreach ($attributes as $att)
        if (!array_key_exists($att, $array))
            return false;

    return true;
}

function is_starts_with($search, $subject) {
  return strpos($search, $subject) === 0;
}

function is_ends_with($search, $subject) {
  return strpos($search, $subject) === strlen($search);
}