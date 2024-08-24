<?php

$variables = [
  'DATABASE_URL' => 'host=localhost port=5432 dbname=extensao-baiucas user=postgres password=admin'
];

foreach ($variables as $key => $value)
  set_env($key, $value);
