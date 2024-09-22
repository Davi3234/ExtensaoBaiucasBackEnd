<?php

$handle = fopen('.env', 'r');

$variables = [];
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    if (!trim($line))
      continue;

    [$line] = explode('#', $line);

    [$name, $value] = explode('=', $line, 2);

    if (!$name || !$value)
      continue;

    $variables[$name] = trim($value, '\'"');
  }

  fclose($handle);
}

foreach ($variables as $key => $value) {
  set_env($key, $value);
}
