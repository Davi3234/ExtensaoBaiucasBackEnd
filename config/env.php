<?php

$handle = fopen('.env', 'r');

$variables = [];
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    if (!trim($line))
      continue;

    [$line] = explode('#', $line, 2);

    [$name, $value] = explode('=', $line, 2);

    $name = trim($name);
    $value = trim($value, '\'"');

    if (!$name || !$value)
      continue;

    $variables[$name] = $value;
  }

  fclose($handle);
}

foreach ($variables as $key => $value) {
  set_env($key, $value);
}